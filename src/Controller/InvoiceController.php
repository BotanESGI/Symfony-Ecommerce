<?php

namespace App\Controller;

use App\Entity\Invoice;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    public function generateInvoicePdf(Invoice $invoice, array $orderItems): Response
    {
        $order = $invoice->getOrder();
        $pdfDir = $this->getParameter('kernel.project_dir') . '/public/invoices/';
        $pdfFilename = 'invoice_' . uniqid() . '.pdf';
        $pdfPath = $pdfDir . $pdfFilename;

        $dompdf = new Dompdf();
        $html = $this->renderView('invoice/invoice.html.twig', [
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
}
