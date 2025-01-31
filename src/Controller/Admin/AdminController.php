<?php

namespace App\Controller\Admin;

use App\Controller\IsGranted;
use App\Entity\Address;
use App\Entity\Cart;
use App\Entity\Category;
use App\Entity\DigitalProduct;
use App\Entity\Invoice;
use App\Entity\Orders;
use App\Entity\PhysicalProduct;
use App\Entity\Product;
use App\Entity\Review;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\AddressBackType;
use App\Form\CartType;
use App\Form\CategoryType;
use App\Form\InvoiceType;
use App\Form\OrderType;
use App\Form\ProductCreateType;
use App\Form\ProductEditType;
use App\Form\ReviewType;
use App\Form\TagType;
use App\Form\UserBackType;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/admin', name: 'admin_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig', [
        ]);
    }

    ////////////////////////////////////////////////
    ///////////////////Produits/////////////////////
    ////////////////////////////////////////////////
    #[Route('/admin/products', name: 'admin_products', methods: ['GET'])]
    public function CRUDAdminProducts(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        return $this->render('admin/product/products.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/admin/products/create', name: 'admin_products_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $productType = $request->query->get('type');
        $defaultCategoryId = 1;
        $defaultCategory = $this->entityManager->getRepository(Category::class)->find($defaultCategoryId);

        // Je cree une categorie par defaut si la category 1 (categorie par defaut) est supprimer exemple, on le change apres dans le ProductCreateType, c'est jutse pour initialiser car c'est obigatoire dans le constructeur, pour pas avoir d'erreur exemple si on supprime une category
       //Requete préparé pour forcer id 1 car il existe pas et je peux pas le set via l'orm
        if (!$defaultCategory) {
            $categoryName = 'catégorie par défaut';
            $defaultColor = '#FFFFFF';
            $connection = $this->entityManager->getConnection();
            $sql = 'INSERT INTO category (id, name, color) VALUES (:id, :name, :color)';
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'id' => $defaultCategoryId,
                'name' => $categoryName,
                'color' => $defaultColor,
            ]);
            $defaultCategory = $this->entityManager->getRepository(Category::class)->find($defaultCategoryId);
        }

        if ($productType === 'digital') {
            $product = new DigitalProduct($defaultCategory);
        } else {
            $product = new PhysicalProduct($defaultCategory);
        }

        $form = $this->createForm(ProductCreateType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('images_directory'), $filename);
                $product->setImage($filename);
            }

            if ($product instanceof DigitalProduct) {
                $product->setDownloadLink($form->get('downloadLink')->getData());
                $product->setFilesize($form->get('filesize')->getData());
                $product->setFiletype($form->get('filetype')->getData());
            } elseif ($product instanceof PhysicalProduct) {
                $characteristicsJson = $form->get('characteristics')->getData();
                if ($characteristicsJson) {
                    $product->setCharacteristics(json_decode($characteristicsJson, true));
                } else {
                    $product->setCharacteristics([]);
                }
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/products_create.html.twig', [
            'form' => $form->createView(),
            'productType' => $productType,
        ]);
    }

    #[Route('/admin/products/edit/{id}', name: 'admin_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            $this->addFlash('error', 'Le produit demandé n\'existe pas.');
            return $this->redirectToRoute('admin_products');
        }

        $form = $this->createForm(ProductEditType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('images_directory'), $filename);
                $product->setImage($filename);
            }

            $product->setDefaultCategory($form->get('defaultCategory')->getData());

            $categories = $form->get('categories')->getData();
            foreach ($categories as $category) {
                $product->addCategory($category);
            }

            if ($product instanceof DigitalProduct) {
                $product->setDownloadLink($form->get('downloadLink')->getData());
                $product->setFilesize($form->get('filesize')->getData());
                $product->setFiletype($form->get('filetype')->getData());
            } elseif ($product instanceof PhysicalProduct) {
                $characteristicsJson = $form->get('characteristics')->getData();
                if ($characteristicsJson) {
                    $product->setCharacteristics(json_decode($characteristicsJson, true));
                } else {
                    $product->setCharacteristics([]);
                }
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/products_edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }


    #[Route('/admin/products/delete/{id}', name: 'admin_products_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            $this->addFlash('error', 'Le produit demandé n\'existe pas.');
            return $this->redirectToRoute('admin_products');
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
            $this->addFlash('success', 'Produit supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_products');
    }

    ////////////////////////////////////////////////
    ///////////////////Avis/////////////////////
    ////////////////////////////////////////////////
    #[Route('/admin/reviews', name: 'admin_reviews', methods: ['GET'])]
    public function CRUDAdminReviews(): Response
    {
        $reviews = $this->entityManager->getRepository(Review::class)->findAll();
        return $this->render('admin/review/reviews.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/admin/reviews/create', name: 'admin_reviews_create', methods: ['GET', 'POST'])]
    public function createReview(Request $request, EntityManagerInterface $em): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'L\'avis a été créé avec succès.');
            return $this->redirectToRoute('admin_reviews');
        }

        return $this->render('admin/review/reviews_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/reviews/edit/{id}', name: 'admin_reviews_edit', methods: ['GET', 'POST'])]
    public function editReview(Request $request, Review $review, EntityManagerInterface $em, int $id): Response
    {
        $review = $this->entityManager->getRepository(Review::class)->find($id);

        if (!$review) {
            $this->addFlash('error', 'L\'avis demandé n\'existe pas.');
            return $this->redirectToRoute('admin_reviews');
        }

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'L\'avis a été modifié avec succès.');
            return $this->redirectToRoute('admin_reviews');
        }

        return $this->render('admin/review/reviews_edit.html.twig', [
            'form' => $form->createView(),
            'review' => $review,
        ]);
    }

    #[Route('/admin/reviews/delete/{id}', name: 'admin_reviews_delete', methods: ['POST'])]
    public function deleteReview(Request $request, int $id): Response
    {
        $review = $this->entityManager->getRepository(Review::class)->find($id);

        if (!$review) {
            $this->addFlash('error', 'L\'avis demandé n\'existe pas.');
            return $this->redirectToRoute('admin_reviews');
        }

        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($review);
            $this->entityManager->flush();
            $this->addFlash('success', 'Avis supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_reviews');
    }

    ////////////////////////////////////////////////
    ///////////////////categorie/////////////////////
    ////////////////////////////////////////////////
    #[Route('/admin/categories', name: 'admin_categories', methods: ['GET'])]
    public function CRUDAdminCategories(): Response
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();
        return $this->render('admin/category/categories.html.twig', [
            'categories' => $categories,
            'all_products'=> $this->entityManager->getRepository(Product::class)->findAll()
        ]);
    }

    #[Route('/admin/categories/create', name: 'admin_categories_create', methods: ['GET', 'POST'])]
    public function createCategory(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'Catégorie créée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/category_create_or_update.html.twig', [
            'form' => $form->createView(),
            'category' => null,
        ]);
    }

    #[Route('/admin/categories/edit/{id}', name: 'admin_categories_edit', methods: ['GET', 'POST'])]
    public function updateCategory(Request $request, int $id): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            $this->addFlash('error', 'La catégorie demandée n\'existe pas.');
            return $this->redirectToRoute('admin_categories');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Catégorie mise à jour avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/category_create_or_update.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }


    #[Route('/admin/categories/delete/{id}', name: 'admin_categories_delete', methods: ['POST'])]
    public function deleteCategory(Request $request, int $id, ProductRepository $productRepository): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            $this->addFlash('error', 'La catégorie demandée n\'existe pas.');
            return $this->redirectToRoute('admin_categories');
        }

        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {

            $products_in_category = $category->getProducts();
            $products_with_default_category = $productRepository->findBy(['defaultCategory' => $id]);

            // Dissocier les produits de la catégorie
            foreach ($products_in_category as $product) {
                $product->removeCategory($category);
            }

            // Supprimer les produits ayant comme catégorie par défaut la catégorie à supprimer
            foreach ($products_with_default_category as $product) {
                $this->entityManager->remove($product);
                $this->entityManager->flush();
            }


            $this->entityManager->remove($category);
            $this->entityManager->flush();
            $this->addFlash('success', 'Catégorie supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_categories');
    }

    ////////////////////////////////////////////////
    ///////////////////Tags/////////////////////
    ////////////////////////////////////////////////
    #[Route('/admin/tags', name: 'admin_tags', methods: ['GET'])]
    public function CRUDAdminTags(): Response
    {
        $tags = $this->entityManager->getRepository(Tag::class)->findAll();
        return $this->render('admin/tag/tags.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/admin/tags/create', name: 'admin_tags_create', methods: ['GET', 'POST'])]
    public function createTag(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $this->addFlash('success', 'Tag créé avec succès.');
            return $this->redirectToRoute('admin_tags');
        }

        return $this->render('admin/tag/tags_create_or_update.html.twig', [
            'form' => $form->createView(),
            'tag' => null,
        ]);
    }

    #[Route('/admin/tags/edit/{id}', name: 'admin_tags_edit', methods: ['GET', 'POST'])]
    public function editTag(Request $request, int $id): Response
    {
        $tag = $this->entityManager->getRepository(Tag::class)->find($id);

        if (!$tag) {
            $this->addFlash('error', 'Le tag demandé n\'existe pas.');
            return $this->redirectToRoute('admin_tags');
        }

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Tag modifié avec succès.');
            return $this->redirectToRoute('admin_tags');
        }

        return $this->render('admin/tag/tags_create_or_update.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag,
        ]);
    }

    #[Route('/admin/tags/delete/{id}', name: 'admin_tags_delete', methods: ['POST'])]
    public function deleteTag(Request $request, int $id): Response
    {
        $tag = $this->entityManager->getRepository(Tag::class)->find($id);

        if (!$tag) {
            $this->addFlash('error', 'Le tag demandé n\'existe pas.');
            return $this->redirectToRoute('admin_tags');
        }

        if ($this->isCsrfTokenValid('delete' . $tag->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($tag);
            $this->entityManager->flush();
            $this->addFlash('success', 'Tag supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_tags');
    }

    ////////////////////////////////////////////////
    ///////////////////Addresses/////////////////////
    ////////////////////////////////////////////////
    #[Route('/admin/addresses', name: 'admin_addresses', methods: ['GET'])]
    public function CRUDAddresses(): Response
    {
        $addresses = $this->entityManager->getRepository(Address::class)->findAll();
        return $this->render('admin/address/address.html.twig', [
            'addresses' => $addresses,
        ]);
    }

    #[Route('/admin/addresses/create', name: 'admin_addresses_create', methods: ['GET', 'POST'])]
    public function createAddresses(Request $request): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressBackType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            $this->addFlash('success', 'Adresse créée avec succès.');
            return $this->redirectToRoute('admin_addresses');
        }

        return $this->render('admin/address/address_create_or_update.html.twig', [
            'form' => $form->createView(),
            'address' => null,
        ]);
    }

    #[Route('/admin/addresses/edit/{id}', name: 'admin_addresses_edit', methods: ['GET', 'POST'])]
    public function editAddresses(Request $request, int $id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->find($id);

        if (!$address) {
            $this->addFlash('error', 'L\'adresse demandée n\'existe pas.');
            return $this->redirectToRoute('admin_addresses');
        }

        $form = $this->createForm(AddressBackType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Adresse mise à jour avec succès.');
            return $this->redirectToRoute('admin_addresses');
        }

        return $this->render('admin/address/address_create_or_update.html.twig', [
            'form' => $form->createView(),
            'address' => $address,
        ]);
    }

    #[Route('/admin/addresses/delete/{id}', name: 'admin_addresses_delete', methods: ['POST'])]
    public function deleteAddresses(Request $request, int $id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->find($id);

        if (!$address) {
            $this->addFlash('error', 'L\'adresse demandée n\'existe pas.');
            return $this->redirectToRoute('admin_addresses');
        }

        if ($this->isCsrfTokenValid('delete' . $address->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($address);
            $this->entityManager->flush();
            $this->addFlash('success', 'Adresse supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_addresses');
    }

    ////////////////////////////////////////////////
    ///////////////////Panier/////////////////////
    ////////////////////////////////////////////////
    #[Route('/admin/cart', name: 'admin_cart', methods: ['GET'])]
    public function CRUDCarts(CartRepository $cartRepository): Response
    {
        $carts = $cartRepository->findAll();
        $cartData = [];

        foreach ($carts as $cart) {
            $cartItems = [];
            $cartTotal = 0;

            foreach ($cart->getCartItems() as $cartItem) {
                $cartItems[] = [
                    'product_name' => $cartItem->getProduct()->getName(),
                    'product_image' => $cartItem->getProduct()->getImage(),
                    'product_price' => $cartItem->getProduct()->getPrice(),
                    'quantity' => $cartItem->getQuantity(),
                    'total_price' => $cartItem->getQuantity() * $cartItem->getProduct()->getPrice(),
                ];
                $cartTotal += $cartItem->getQuantity() * $cartItem->getProduct()->getPrice();
            }

            $cartData[] = [
                'cart_id' => $cart->getId(),
                'user' => [
                    'name' => $cart->getUser()->getName(),
                    'lastname' => $cart->getUser()->getLastname(),
                ],
                'cart_items' => $cartItems,
                'cart_total' => $cartTotal,
            ];
        }

        return $this->render('admin/cart/carts.html.twig', [
            'cartData' => $cartData,
        ]);
    }


    #[Route('/admin/cart/create', name: 'admin_cart_create', methods: ['GET', 'POST'])]
    public function createCart(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cart = new Cart();
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $cart->getUser();

            // Récupérer tous les paniers existants de l'utilisateur
            $existingCarts = $entityManager->getRepository(Cart::class)->findBy(['user' => $user]);

            if ($existingCarts) {
                foreach ($existingCarts as $existingCart) {
                    $entityManager->remove($existingCart);
                }
                $entityManager->flush();
            }

            foreach ($cart->getCartItems() as $cartItem) {
                $cartItem->setCart($cart);
            }

            $entityManager->persist($cart);
            $entityManager->flush();

            $this->addFlash('success', 'Panier créé avec succès.');
            return $this->redirectToRoute('admin_cart');
        }

        return $this->render('admin/cart/carts_create_or_update.html.twig', [
            'form' => $form->createView(),
            'cart' => null,
        ]);
    }


    #[Route('/admin/cart/edit/{id}', name: 'admin_cart_update', methods: ['GET', 'POST'])]
    public function updateCart(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $cart = $entityManager->getRepository(Cart::class)->find($id);

        if (!$cart) {
            $this->addFlash('error', 'Le panier demandé n\'existe pas.');
            return $this->redirectToRoute('admin_cart');
        }
        $originalItems = new ArrayCollection();

        foreach ($cart->getCartItems() as $cartItem) {
            $originalItems->add($cartItem);
        }

        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $newUser = $form->get('user')->getData();

            // Récupérer tous les paniers existants de l'utilisateur
            $existingCarts = $entityManager->getRepository(Cart::class)->findBy(['user' => $newUser]);

            if ($existingCarts) {
                foreach ($existingCarts as $existingCart) {
                    $entityManager->remove($existingCart);
                }
                $entityManager->flush();
            }

            foreach ($cart->getCartItems() as $cartItem) {
                $cartItem->setCart($cart);
            }

            foreach ($originalItems as $cartItem) {
                if (!$cart->getCartItems()->contains($cartItem)) {
                    $entityManager->remove($cartItem);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Panier mis à jour avec succès.');
            return $this->redirectToRoute('admin_cart');
        }

        return $this->render('admin/cart/carts_create_or_update.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart,
        ]);
    }


    #[Route('/admin/cart/delete/{id}', name: 'admin_cart_delete', methods: ['POST'])]
    public function deleteCart(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $cart = $entityManager->getRepository(Cart::class)->find($id);

        if (!$cart) {
            $this->addFlash('error', 'Le panier demandé n\'existe pas.');
            return $this->redirectToRoute('admin_cart');
        }

        if (!$cart) {
            $this->addFlash('error', 'Le panier demandé n\'existe pas.');
            return $this->redirectToRoute('admin_cart');
        }

        if ($this->isCsrfTokenValid('delete' . $cart->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cart);
            $entityManager->flush();

            $this->addFlash('success', 'Panier supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_cart');
    }

    ////////////////////////////////////////////////
    ///////////////////Facture/////////////////////
    ////////////////////////////////////////////////
    #[Route('/admin/invoices', name: 'admin_invoices', methods: ['GET'])]
    public function CRUDInvoices(): Response
    {
        $invoices = $this->entityManager->getRepository(Invoice::class)->findAll();
        return $this->render('admin/invoice/invoices.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/admin/invoices/create', name: 'admin_invoices_create', methods: ['GET', 'POST'])]
    public function createInvoice(Request $request): Response
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->get('order')->getData();

            // Vérifier si cette commande est déjà associée à une facture
            $existingInvoice = $this->entityManager->getRepository(Invoice::class)->findOneBy(['order' => $order]);

            if ($existingInvoice) {
                $this->addFlash('error', 'Cette commande est déjà associée à une facture.');
            } else {
                $this->entityManager->persist($invoice);
                $this->entityManager->flush();
                $this->addFlash('success', 'Facture créée avec succès.');
                return $this->redirectToRoute('admin_invoices');
            }
        }

        return $this->render('admin/invoice/invoices_create_or_update.html.twig', [
            'form' => $form->createView(),
            'invoice' => null,
        ]);
    }


    #[Route('/admin/invoices/edit/{id}', name: 'admin_invoices_edit', methods: ['GET', 'POST'])]
    public function editInvoice(Request $request, int $id): Response
    {
        $invoice = $this->entityManager->getRepository(Invoice::class)->find($id);

        if (!$invoice) {
            $this->addFlash('error', 'Facture non trouvée.');
            return $this->redirectToRoute('admin_invoices');
        }

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newOrder = $form->get('order')->getData();

            // Vérifier si la nouvelle commande est déjà associée à une autre facture
            $existingInvoice = $this->entityManager->getRepository(Invoice::class)->findOneBy(['order' => $newOrder]);

            if ($existingInvoice && $existingInvoice->getId() !== $invoice->getId()) {
                $this->addFlash('error', 'Cette commande est déjà associée à une autre facture.');
            } else {
                $this->entityManager->flush();
                $this->addFlash('success', 'Facture modifiée avec succès.');
                return $this->redirectToRoute('admin_invoices');
            }
        }

        return $this->render('admin/invoice/invoices_create_or_update.html.twig', [
            'form' => $form->createView(),
            'invoice' => $invoice,
        ]);
    }

    #[Route('/admin/invoices/delete/{id}', name: 'admin_invoices_delete', methods: ['POST', 'GET'])]
    public function deleteInvoice(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $invoice = $this->entityManager->getRepository(Invoice::class)->find($id);

        if (!$invoice) {
            $this->addFlash('error', 'Facture non trouvée.');
            return $this->redirectToRoute('admin_invoices');
        }

        $invoiceId = $invoice->getId();
        $order = $invoice->getOrder();

        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->request->get('_token'))) {
            if ($order)
            {
                $orderId = $order->getId();
                $entityManager->getConnection()->executeStatement(
                    'UPDATE "invoice" SET "order_id" = NULL WHERE "id" = :id',
                    ['id' => $invoiceId]
                );
                $entityManager->getConnection()->executeStatement(
                    'UPDATE "orders" SET "invoice_id" = NULL WHERE "id" = :id',
                    ['id' => $orderId]
                );

                $entityManager->getConnection()->executeStatement(
                    'DELETE FROM "invoice" WHERE "id" = :id',
                    ['id' => $invoiceId]
                );
            }
            else
            {
                $entityManager->getConnection()->executeStatement(
                    'DELETE FROM "invoice" WHERE "id" = :id',
                    ['id' => $invoiceId]
                );
            }
            $this->addFlash('success', 'Facture supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_invoices');
    }

    ////////////////////////////////////////////////
    ///////////////////Commandes/////////////////////
    ////////////////////////////////////////////////

    #[Route('/admin/orders', name: 'admin_orders', methods: ['GET'])]
    public function CRUDOrders(OrdersRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAll();
        $orderData = [];

        foreach ($orders as $order) {
            $orderItems = [];
            foreach ($order->getOrderItems() as $orderItem) {
                $orderItems[] = [
                    'product_name' => $orderItem->getProduct()->getName(),
                    'product_image' => $orderItem->getProduct()->getImage(),
                    'product_price' => $orderItem->getProduct()->getPrice(),
                    'quantity' => $orderItem->getQuantity(),
                    'total_price' => $orderItem->getQuantity() * $orderItem->getProduct()->getPrice(),
                ];
            }

            $invoice = $order->getInvoice();
            if ($invoice === null || $invoice->getPdfPath() === null) {
                $invoice_pdf = null;
            }
            else
            {
                $pdfDir = $this->getParameter('kernel.project_dir') . '/public';
                $pdfPath = $pdfDir . $invoice->getPdfPath();
                if (!file_exists($pdfPath)) {
                    $invoice_pdf = null;
                }
                else
                {
                    $invoice_pdf = $invoice->getPdfPath();
                }
            }

            $orderData[] = [
                'order_id' => $order->getId(),
                'user' => [
                    'name' => $order->getUser()->getName(),
                    'lastname' => $order->getUser()->getLastname(),
                ],
                'order_items' => $orderItems,
                'invoice_pdf' => $invoice_pdf,
                'total' => $order->getTotal(),
                'date' => $order->getDate()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->render('admin/order/orders.html.twig', [
            'orderData' => $orderData,
        ]);
    }

    #[Route('/admin/orders/create', name: 'admin_order_create', methods: ['GET', 'POST'])]
    public function createOrder(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Orders();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setTotal(array_reduce($order->getOrderItems()->toArray(), function ($sum, $item) {
                return $sum + ($item->getQuantity() * $item->getProduct()->getPrice());
            }, 0));

            $entityManager->persist($order);
            $entityManager->flush();

            $this->addFlash('success', 'Commande créée avec succès.');
            return $this->redirectToRoute('admin_orders');
        }

        return $this->render('admin/order/orders_create_or_update.html.twig', [
            'form' => $form->createView(),
            'order' => null,
        ]);
    }

    #[Route('/admin/orders/edit/{id}', name: 'admin_order_update', methods: ['GET', 'POST'])]
    public function updateOrder(Request $request, int $id, EntityManagerInterface $entityManager, OrdersRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        if (!$order) {
            $this->addFlash('error', 'La commande demandée n\'existe pas.');
            return $this->redirectToRoute('admin_orders');
        }
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setTotal(array_reduce($order->getOrderItems()->toArray(), function ($sum, $item) {
                return $sum + ($item->getQuantity() * $item->getProduct()->getPrice());
            }, 0));

            $entityManager->flush();

            $this->addFlash('success', 'Commande mise à jour avec succès.');
            return $this->redirectToRoute('admin_orders');
        }

        return $this->render('admin/order/orders_create_or_update.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    #[Route('/admin/orders/delete/{id}', name: 'admin_order_delete', methods: ['POST', 'GET'])]
    public function deleteOrder(Request $request, int $id, EntityManagerInterface $entityManager, OrdersRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        if (!$order) {
            $this->addFlash('error', 'La commande demandée n\'existe pas.');
            return $this->redirectToRoute('admin_orders');
        }

        $orderId = $order->getId();

        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $invoice = $order->getInvoice();
            if ($invoice) {
                $invoiceId = $invoice->getId();
                $entityManager->getConnection()->executeStatement(
                    'UPDATE "invoice" SET "order_id" = NULL WHERE "id" = :id',
                    ['id' => $invoiceId]
                );
                $entityManager->getConnection()->executeStatement(
                    'UPDATE "orders" SET "invoice_id" = NULL WHERE "id" = :id',
                    ['id' => $orderId]
                );

                $entityManager->getConnection()->executeStatement(
                    'DELETE FROM "orders" WHERE "id" = :id',
                    ['id' => $orderId]
                );

                $entityManager->getConnection()->executeStatement(
                    'DELETE FROM "invoice" WHERE "id" = :id',
                    ['id' => $invoiceId]
                );
            }
            else
            {
                $entityManager->getConnection()->executeStatement(
                    'UPDATE "orders" SET "invoice_id" = NULL WHERE "id" = :id',
                    ['id' => $orderId]
                );

                $entityManager->getConnection()->executeStatement(
                    'DELETE FROM "orders" WHERE "id" = :id',
                    ['id' => $orderId]
                );
            }
            $this->addFlash('success', 'Commande supprimées avec succès.');
        }

        return $this->redirectToRoute('admin_orders');
    }

    ////////////////////////////////////////////////
    ///////////////////Utilisateur/////////////////////
    ////////////////////////////////////////////////
    #[Route('/admin/users', name: 'admin_users', methods: ['GET'])]
    public function CRUDUser(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $this->render('admin/user/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/create', name: 'admin_user_create', methods: ['GET', 'POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserBackType::class, $user, ['is_create' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            if (!empty($password)) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur creé avec succès.');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user/user_create_or_update.html.twig', [
            'user' => $user,
            'form' => $form,
            'is_edit' => false,
        ]);
    }

    #[Route('/admin/users/edit/{id}', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function updateUser(Request $request, int $id, User $user, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'L\'utilisateur n\'existe pas.');
            return $this->redirectToRoute('admin_users');
        }

        $form = $this->createForm(UserBackType::class, $user, ['is_create' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('password')->getData()) {
                $plainPassword = $form->get('password')->getData();
                $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
                $user->setPassword($hashedPassword);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user/user_create_or_update.html.twig', [
            'user' => $user,
            'form' => $form,
            'is_edit' => true,
        ]);
    }

    #[Route('/admin/users/delete/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'L\'utilisateur n\'existe pas.');
            return $this->redirectToRoute('admin_users');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }
        $request->getSession()->invalidate();
        return $this->redirectToRoute('admin_users');
    }
}
