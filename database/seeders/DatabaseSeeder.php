<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = $this->seedUser(
            name: env('STORENAV_ADMIN_NAME', 'Veripay Admin'),
            email: env('STORENAV_ADMIN_EMAIL', 'admin@veripay.test'),
            password: env('STORENAV_ADMIN_PASSWORD'),
            role: User::ROLE_ADMIN,
            requirePassword: app()->environment('production')
        );

        $this->seedUser(
            name: env('STORENAV_SECURITY_NAME', 'Security Staff'),
            email: env('STORENAV_SECURITY_EMAIL', 'security@veripay.test'),
            password: env('STORENAV_SECURITY_PASSWORD'),
            role: User::ROLE_STAFF,
            requirePassword: app()->environment('production')
        );

        if (! app()->environment('production') || filter_var(env('STORENAV_SEED_DEMO_CUSTOMER', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->seedUser(
                name: env('STORENAV_CUSTOMER_NAME', 'Demo Customer'),
                email: env('STORENAV_CUSTOMER_EMAIL', 'customer@veripay.test'),
                password: env('STORENAV_CUSTOMER_PASSWORD', 'password'),
                role: User::ROLE_CUSTOMER,
                budgetLimit: 50
            );
        }

        $categories = collect([
            'Fresh Produce',
            'Beverages',
            'Snacks',
            'Household',
            'Frozen',
        ])->mapWithKeys(function (string $name) {
            return [$name => Category::firstOrCreate(['name' => $name])];
        });

        foreach ($this->products($categories) as $product) {
            Product::updateOrCreate(
                ['barcode' => $product['barcode']],
                $product
            );
        }

        $this->command?->info('Seeded Veripay users, categories, and products.');
        $this->command?->line('Admin email: ' . $admin->email);
    }

    private function seedUser(
        string $name,
        string $email,
        ?string $password,
        string $role,
        ?float $budgetLimit = null,
        bool $requirePassword = false
    ): User {
        if ($requirePassword && blank($password) && ! User::where('email', $email)->exists()) {
            throw new RuntimeException("Set a password for {$email} before running production seeders.");
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->fill([
            'name' => $name,
            'role' => $role,
            'budget_limit' => $budgetLimit,
            'email_verified_at' => $user->email_verified_at ?: now(),
        ]);

        if (! $user->exists || filled($password)) {
            $user->password = Hash::make($password ?: 'password');
        }

        $user->save();

        return $user;
    }

    private function products($categories): array
    {
        $produce = $categories['Fresh Produce']->id;
        $bev = $categories['Beverages']->id;
        $snack = $categories['Snacks']->id;
        $house = $categories['Household']->id;
        $frozen = $categories['Frozen']->id;

        return [
            ['name' => 'Organic Apples', 'description' => 'Crisp orchard apples.', 'price' => 1.99, 'barcode' => 'FP-10001', 'category_id' => $produce, 'stock_quantity' => 120, 'is_active' => true],
            ['name' => 'Banana Bunch', 'description' => 'Ripe yellow bananas.', 'price' => 1.49, 'barcode' => 'FP-10002', 'category_id' => $produce, 'stock_quantity' => 140, 'is_active' => true],
            ['name' => 'Fresh Lettuce', 'description' => 'Crisp romaine heads.', 'price' => 2.29, 'barcode' => 'FP-10003', 'category_id' => $produce, 'stock_quantity' => 90, 'is_active' => true],
            ['name' => 'Cherry Tomatoes', 'description' => 'Sweet snack tomatoes.', 'price' => 2.89, 'barcode' => 'FP-10004', 'category_id' => $produce, 'stock_quantity' => 80, 'is_active' => true],
            ['name' => 'Avocados', 'description' => 'Popular choice.', 'price' => 3.80, 'barcode' => 'FP-10010', 'category_id' => $produce, 'stock_quantity' => 90, 'is_active' => true],
            ['name' => 'Baby Spinach', 'description' => 'Popular choice.', 'price' => 3.20, 'barcode' => 'FP-10011', 'category_id' => $produce, 'stock_quantity' => 90, 'is_active' => true],

            ['name' => 'Sparkling Water', 'description' => 'Refreshing sparkling water.', 'price' => 2.50, 'barcode' => 'BV-20001', 'category_id' => $bev, 'stock_quantity' => 120, 'is_active' => true],
            ['name' => 'Orange Juice', 'description' => '100% squeezed juice.', 'price' => 3.75, 'barcode' => 'BV-20002', 'category_id' => $bev, 'stock_quantity' => 90, 'is_active' => true],
            ['name' => 'Cold Brew Coffee', 'description' => 'Smooth and bold.', 'price' => 4.50, 'barcode' => 'BV-20003', 'category_id' => $bev, 'stock_quantity' => 70, 'is_active' => true],
            ['name' => 'Sports Drink', 'description' => 'Electrolyte boost.', 'price' => 2.10, 'barcode' => 'BV-20004', 'category_id' => $bev, 'stock_quantity' => 110, 'is_active' => true],
            ['name' => 'Soda Mix Pack', 'description' => 'Assorted sodas.', 'price' => 6.99, 'barcode' => 'BV-20005', 'category_id' => $bev, 'stock_quantity' => 60, 'is_active' => true],
            ['name' => 'Iced Tea', 'description' => 'Popular choice.', 'price' => 2.20, 'barcode' => 'BV-20010', 'category_id' => $bev, 'stock_quantity' => 90, 'is_active' => true],
            ['name' => 'Mineral Water', 'description' => 'Popular choice.', 'price' => 1.80, 'barcode' => 'BV-20011', 'category_id' => $bev, 'stock_quantity' => 90, 'is_active' => true],

            ['name' => 'Trail Mix', 'description' => 'Protein-packed snack.', 'price' => 4.20, 'barcode' => 'SN-30001', 'category_id' => $snack, 'stock_quantity' => 60, 'is_active' => true],
            ['name' => 'Potato Chips', 'description' => 'Sea salt classic.', 'price' => 3.10, 'barcode' => 'SN-30002', 'category_id' => $snack, 'stock_quantity' => 85, 'is_active' => true],
            ['name' => 'Granola Bars', 'description' => 'Honey oat bars.', 'price' => 3.65, 'barcode' => 'SN-30003', 'category_id' => $snack, 'stock_quantity' => 75, 'is_active' => true],
            ['name' => 'Chocolate Cookies', 'description' => 'Baked daily.', 'price' => 3.90, 'barcode' => 'SN-30004', 'category_id' => $snack, 'stock_quantity' => 55, 'is_active' => true],
            ['name' => 'Pretzel Twists', 'description' => 'Salty crunch.', 'price' => 2.75, 'barcode' => 'SN-30005', 'category_id' => $snack, 'stock_quantity' => 65, 'is_active' => true],
            ['name' => 'Cereal Crunch', 'description' => 'Popular choice.', 'price' => 4.10, 'barcode' => 'SN-30010', 'category_id' => $snack, 'stock_quantity' => 90, 'is_active' => true],
            ['name' => 'Energy Bar', 'description' => 'Popular choice.', 'price' => 2.60, 'barcode' => 'SN-30011', 'category_id' => $snack, 'stock_quantity' => 90, 'is_active' => true],

            ['name' => 'Laundry Detergent', 'description' => 'High-efficiency formula.', 'price' => 9.99, 'barcode' => 'HH-40001', 'category_id' => $house, 'stock_quantity' => 40, 'is_active' => true],
            ['name' => 'Dish Soap', 'description' => 'Lemon fresh.', 'price' => 2.49, 'barcode' => 'HH-40002', 'category_id' => $house, 'stock_quantity' => 90, 'is_active' => true],
            ['name' => 'Paper Towels', 'description' => 'Ultra absorbent.', 'price' => 5.50, 'barcode' => 'HH-40003', 'category_id' => $house, 'stock_quantity' => 55, 'is_active' => true],
            ['name' => 'Glass Cleaner', 'description' => 'Streak-free shine.', 'price' => 3.20, 'barcode' => 'HH-40004', 'category_id' => $house, 'stock_quantity' => 50, 'is_active' => true],
            ['name' => 'Floor Cleaner', 'description' => 'Popular choice.', 'price' => 4.20, 'barcode' => 'HH-40010', 'category_id' => $house, 'stock_quantity' => 90, 'is_active' => true],
            ['name' => 'Sponges', 'description' => 'Popular choice.', 'price' => 1.90, 'barcode' => 'HH-40011', 'category_id' => $house, 'stock_quantity' => 90, 'is_active' => true],

            ['name' => 'Frozen Pizza', 'description' => 'Stone-baked crust.', 'price' => 6.49, 'barcode' => 'FZ-50001', 'category_id' => $frozen, 'stock_quantity' => 70, 'is_active' => true],
            ['name' => 'Ice Cream Tub', 'description' => 'Vanilla bean.', 'price' => 5.99, 'barcode' => 'FZ-50002', 'category_id' => $frozen, 'stock_quantity' => 65, 'is_active' => true],
            ['name' => 'Frozen Veggie Mix', 'description' => 'Steam-ready blend.', 'price' => 3.99, 'barcode' => 'FZ-50003', 'category_id' => $frozen, 'stock_quantity' => 80, 'is_active' => true],
            ['name' => 'Fish Fillets', 'description' => 'Wild-caught.', 'price' => 8.49, 'barcode' => 'FZ-50004', 'category_id' => $frozen, 'stock_quantity' => 45, 'is_active' => true],
            ['name' => 'Frozen Waffles', 'description' => 'Popular choice.', 'price' => 4.90, 'barcode' => 'FZ-50010', 'category_id' => $frozen, 'stock_quantity' => 90, 'is_active' => true],
            ['name' => 'Frozen Berries', 'description' => 'Popular choice.', 'price' => 5.30, 'barcode' => 'FZ-50011', 'category_id' => $frozen, 'stock_quantity' => 90, 'is_active' => true],
        ];
    }
}
