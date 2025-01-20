<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Orders;
use App\Entity\OrderItem;
use App\Entity\Review;
use App\Entity\Address;
use App\Entity\PhysicalProduct;
use App\Entity\DigitalProduct;
use App\Entity\Category;
use App\Entity\Invoice;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Enum\ReviewStatusEnum;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Catégories
        $categories = [];
        $categoryNames = ['Electronics', 'Home Appliances', 'Books', 'Toys', 'Fashion'];

        foreach ($categoryNames as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Utilisateurs
        $users = [];
        $names = [
            ['Botan', 'ESGI'],
            ['Jane', 'Smith'],
            ['Alice', 'Johnson'],
            ['Bob', 'Brown'],
            ['Emily', 'Davis'],
        ];

        foreach ($names as $index => [$firstName, $lastName]) {
            $user = new User();
            $user->setEmail("user{$index}@example.com");
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setRoles(['ROLE_USER']);
            $user->setName($firstName);
            $user->setLastName($lastName);
            $user->setIsVerified(true);
            $manager->persist($user);
            $users[] = $user;
        }

        // Utilisateur Admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPassword(password_hash('admin', PASSWORD_BCRYPT));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setName('Admin');
        $admin->setLastName('User');
        $admin->setIsVerified(true);
        $manager->persist($admin);

        // Utilisateur Banni
        $bannedUser = new User();
        $bannedUser->setEmail('banned@gmail.com');
        $bannedUser->setPassword(password_hash('banned', PASSWORD_BCRYPT));
        $bannedUser->setRoles(['ROLE_BANNED']);
        $bannedUser->setName('Banned');
        $bannedUser->setLastName('User');
        $bannedUser->setIsVerified(false);
        $manager->persist($bannedUser);

        // Produits
        $productsData = [
            ['type' => 'PHYSICAL', 'name' => 'Headphones', 'description' => 'Noise-cancelling headphones', 'price' => 199.99, 'weight' => 0.5, 'dimensions' => '20x10x10 cm'],
            ['type' => 'DIGITAL', 'name' => 'Laptop', 'description' => 'High performance laptop', 'price' => 999.99, 'fileSize' => 2.5, 'downloadLink' => 'http://example.com/laptop-download'],
            ['type' => 'DIGITAL', 'name' => 'Smartphone', 'description' => 'Latest model smartphone', 'price' => 799.99, 'fileSize' => 1.5, 'downloadLink' => 'http://example.com/smartphone-download'],
        ];

        $products = [];
        foreach ($productsData as $data) {
            if ($data['type'] === 'PHYSICAL') {
                $product = new PhysicalProduct();
                $product->setWeight($data['weight']);
                $product->setDimensions($data['dimensions']);
            } else {
                $product = new DigitalProduct();
                $product->setFileSize($data['fileSize']);
                $product->setDownloadLink($data['downloadLink']);
            }

            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setPrice($data['price']);
            $product->setImage('https://picsum.photos/200/300?random=' . rand(1, 100));


            $category = $categories[array_rand($categories)];
            $product->addCategory($category);

            // Tags
            $tags = [];
            for ($i = 0; $i < 2; $i++) {
                $tag = new Tag();
                $tag->setName('Tag ' . rand(1, 10));
                $manager->persist($tag);
                $tags[] = $tag;
            }

            foreach ($tags as $tag) {
                $product->addTag($tag);
            }

            $manager->persist($product);
            $products[] = $product;
        }

        // Adresses
        foreach ($users as $index => $user) {
            $address = new Address();
            $address->setStreet('Street ' . ($index + 1) . ' Main St');
            $address->setCity('City ' . ($index + 1));
            $address->setPostalCode('1000' . $index);
            $address->setUser($user);
            $manager->persist($address);
        }

        // Paniers
        foreach ($users as $user) {
            $cart = new Cart();
            $cart->setUser($user);
            $manager->persist($cart);

            // Articles de panier
            foreach (array_rand($products, 2) as $index) {
                $cartItem = new CartItem();
                $cartItem->setProduct($products[$index]);
                $cartItem->setQuantity(rand(1, 3));
                $cartItem->setCart($cart);
                $manager->persist($cartItem);
            }
        }

        // Commandes
        foreach ($users as $user) {
            $order = new Orders();
            $order->setUser($user);
            $order->setTotal(rand(100, 1000));
            $order->setDate(new \DateTimeImmutable());
            $manager->persist($order);

            // Articles de commande
            foreach (array_rand($products, 2) as $index) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($products[$index]);
                $orderItem->setQuantity(rand(1, 3));
                $orderItem->setOrder($order);
                $orderItem->setPrice($products[$index]->getPrice());
                $manager->persist($orderItem);
            }

            //Avis
            foreach ($products as $product) {
                $review = new Review();
                $review->setContent('Great product!');
                $review->setRating(rand(1, 5));
                $review->setUser($user);
                $review->setProduct($product);
                $review->setStatus(ReviewStatusEnum::VALIDATED);
                $manager->persist($review);
            }

            // Facture
            $invoice = new Invoice();
            $invoice->setTotalAmount($order->getTotal());
            $invoice->setUser($user);
            $manager->persist($invoice);
        }

        $manager->flush();
    }
}
