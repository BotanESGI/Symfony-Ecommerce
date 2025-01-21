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
        // Couleurs catégories
        $colors = ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#FFC300', '#581845', '#DAF7A6', '#C70039', '#900C3F', '#2E86C1'];

        // Catégories
        $categories = [];
        $categoryNames = [
            'Electronics', 'Home Appliances', 'Books', 'Toys', 'Fashion', 'Sports', 'Beauty', 'Automotive', 'Health', 'Garden',
            'Furniture', 'Pets', 'Groceries', 'Jewelry', 'Music', 'Movies', 'Tools', 'Office Supplies', 'Travel', 'Baby Products'
        ];

        foreach ($categoryNames as $index => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setColor($colors[$index % count($colors)]); // Assigner une couleur
            $manager->persist($category);
            $categories[] = $category;
        }

        // Utilisateurs
        $users = [];
        $names = [
            ['Botan', 'ESGI'], ['Jane', 'Smith'], ['Alice', 'Johnson'], ['Bob', 'Brown'], ['Emily', 'Davis'],
            ['Michael', 'Clark'], ['Sarah', 'Wilson'], ['David', 'Hall'], ['Laura', 'Taylor'], ['Chris', 'Martinez']
        ];

        foreach ($names as $index => [$firstName, $lastName]) {
            $user = new User();
            $user->setEmail("user{$index}@example.com");
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setRoles(['ROLE_USER']);
            $user->setName($firstName);
            $user->setLastname($lastName);
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

        $productsData = [];
        $productNames = [
            'Headphones', 'Laptop', 'Smartphone', 'Camera', 'Tablet', 'Smartwatch', 'Printer', 'Router', 'Monitor', 'Keyboard',
            'Mouse', 'Speaker', 'Desk', 'Chair', 'Shelf', 'Lamp', 'Backpack', 'Sunglasses', 'Shoes', 'Jacket'
        ];

        foreach ($productNames as $index => $name) {
            $type = rand(0, 1) ? 'PHYSICAL' : 'DIGITAL';
            $data = [
                'type' => $type,
                'name' => $name,
                'description' => "{$name} description",
                'price' => rand(50, 1000),
            ];

            if ($type === 'PHYSICAL') {
                $data['weight'] = rand(1, 10);
                $data['dimensions'] = rand(10, 100) . 'x' . rand(10, 100) . 'x' . rand(10, 100) . ' cm';
            } else {
                $data['fileSize'] = rand(1, 5);
                $data['downloadLink'] = "http://example.com/{$name}-download";
            }

            $productsData[] = $data;
        }

        $products = [];
        $defaultCategory = $categories[array_rand($categories)];

        foreach ($productsData as $data) {
            if ($data['type'] === 'PHYSICAL') {
                $product = new PhysicalProduct($defaultCategory);
                $product->setWeight($data['weight']);
                $product->setDimensions($data['dimensions']);
            } else {
                $product = new DigitalProduct($defaultCategory);
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

            // Avis
            foreach ($products as $product) {
                $review = new Review();
                $review->setContent('Great product!');
                $review->setRating(rand(1, 5));
                $review->setUser($user);
                $review->setProduct($product);
                $review->setStatus(ReviewStatusEnum::VALIDATED);
                $review->setDatePublication(new \DateTime());
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
