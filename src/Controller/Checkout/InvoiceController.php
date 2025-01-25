<?php

namespace App\Controller\Checkout;

use App\Entity\Invoice;
use App\Entity\Orders;
use App\Service\CartService;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function generateInvoicePdf(Invoice $invoice, array $orderItems): Response
    {
        $order = $invoice->getOrder();
        $pdfDir = $this->getParameter('kernel.project_dir') . '/public/invoices/';
        $pdfFilename = 'invoice_' . uniqid() . '.pdf';
        $pdfPath = $pdfDir . $pdfFilename;

        $dompdf = new Dompdf();
        $html = $this->renderView('checkout/invoice/invoice.html.twig', [
            'order' => $order,
            'invoice' => $invoice,
            'orderItems' => $orderItems
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        file_put_contents($pdfPath, $dompdf->output());

        return $this->json(['path' => '/invoices/' . $pdfFilename]);
    }

    #[Route('profile/order_history/invoice/view/{id}', name: 'show_invoice')]
    public function showInvoice(Request $request, Orders $order): Response
    {
        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        $referer = $request->headers->get('referer') ?: $this->generateUrl('home_page');

        $user = $this->getUser();
        if (!$user) {
            return $this->redirect($referer);
        }

        $invoice = $order->getInvoice();
        if ($invoice === null || $invoice->getPdfPath() === null) {
            throw $this->createNotFoundException('Aucune facture trouvée pour cette commande.');
        }

        if ($order->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette facture.');
        }


        $pdfDir = $this->getParameter('kernel.project_dir') . '/public';
        $pdfPath = $pdfDir . $invoice->getPdfPath();

        if (!file_exists($pdfPath)) {
            throw $this->createNotFoundException('Le fichier PDF de la facture n\'existe pas.' . $pdfPath);
        }

        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        return $this->render('profile/order_history/invoice/invoice.html.twig', [
            'pdfPath' => $invoice->getPdfPath(),
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }
}
