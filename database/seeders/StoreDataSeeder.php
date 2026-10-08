<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StoreDataSeeder extends Seeder
{
    /**
     * Run the database seeds based on the latest database dump.
     */
    public function run(): void
    {
        // 1. SEED USERS
        $users = [
            [
                'id' => 1,
                'name' => 'Krystalll',
                'username' => 'admin',
                'email' => 'admin@luxelips.com',
                'phone' => '0901234567',
                'avatar' => 'avatars/3FQiSw87m6SbFpf7mV3NR4ZKw9BBTxTC19O4nVnD.jpg',
                'password' => '$2y$12$QCVbV0iJ7mNIdxyhA.kM5exqrROMwlUqM4pVq2FcTJRRtfU3HP6IW', // 12345678
                'role' => 'admin',
                'email_verified_at' => '2026-09-02 19:05:37',
            ],
            [
                'id' => 5,
                'name' => 'nguyen son',
                'username' => 'sonneee',
                'email' => 'sonnas14102005@gmail.com',
                'phone' => '0346985803',
                'avatar' => null,
                'password' => '$2y$12$Xn6y56H3g0njXGQ6ePSKPuMrZuY2mb.TnnG75l9ju18TdMefKN.dy',
                'role' => 'user',
                'email_verified_at' => '2026-09-02 19:05:37',
            ],
            [
                'id' => 7,
                'name' => 'admin',
                'username' => 'admin1',
                'email' => 'babycute14102005@gmail.com',
                'phone' => '0346985804',
                'avatar' => null,
                'password' => '$2y$12$0WECiofNF1ko0EGqo/Ytb.lFO4wxzY5vnAw3CfQST4zjAcs9EuZJq',
                'role' => 'admin',
                'email_verified_at' => '2026-10-05 04:04:25',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                $u
            );
        }

        // 2. SEED BRANDS
        $brands = [
            [
                'id' => 1,
                'name' => '3CE',
                'slug' => '3ce',
                'origin' => 'Hàn quốc',
                'description' => null,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'M·A·C Cosmetics',
                'slug' => 'mac-cosmetics',
                'origin' => 'Mỹ',
                'description' => 'Thương hiệu son môi và trang điểm chuyên nghiệp hàng đầu thế giới',
                'is_active' => true,
            ],
            [
                'id' => 4,
                'name' => 'Romand',
                'slug' => 'romand',
                'origin' => 'Hàn Quốc',
                'description' => null,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'name' => 'Peripera',
                'slug' => 'peripera',
                'origin' => 'Hàn Quốc',
                'description' => "Peripera là thương hiệu sở hữu các dòng sản phẩm trang điểm trẻ trung, phù hợp sử dụng hàng ngày. Peripera phản ánh xu hướng và nhịp sống hiện đại, năng động của các cô gái trẻ Hàn Quốc.\n\nLà thương hiệu của xu hướng với sự ra đời của nhiều sản phẩm make-up tập trung vào phong cách mới lạ, dẫn đầu xu hướng được giới trẻ Hàn Quốc đón nhận nhiệt tình.\nSắc màu vui nhộn, trẻ trung mang đến “make-up look” thật tươi sáng, tràn đầy năng lượng cho các cô gái trẻ yêu làm đẹp.\nPhong cách make-up thời thượng giúp bạn thỏa sức thay đổi phong cách từ hiện đại, dễ thương, cá tính đến quyến rữ, nữ tính trong những buổi đi chơi, tiệc tùng…",
                'is_active' => true,
            ],
            [
                'id' => 6,
                'name' => 'Maybelline',
                'slug' => 'maybelline',
                'origin' => 'Mỹ',
                'description' => 'Maybelline là thương hiệu trang điểm số 1 thế giới, đã có mặt tại Việt Nam từ năm 2007 và nhanh chóng trở thành lựa chọn hàng đầu với các sản phẩm như mascara, kem nền, son kem lì. Ứng dụng công nghệ tiên tiến, Maybelline mang đến sản phẩm chất lượng, giá hợp lý, dẫn đầu xu hướng makeup.',
                'is_active' => true,
            ],
        ];

        foreach ($brands as $b) {
            Brand::updateOrCreate(
                ['slug' => $b['slug']],
                $b
            );
        }

        // 3. SEED CATEGORIES
        $categories = [
            [
                'id' => 5,
                'name' => 'Son Thỏi',
                'slug' => 'son-thoi',
                'is_active' => true,
            ],
            [
                'id' => 12,
                'name' => 'Son Kem',
                'slug' => 'son-kem',
                'is_active' => true,
            ],
            [
                'id' => 13,
                'name' => 'Son tint',
                'slug' => 'son-tint',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(
                ['slug' => $c['slug']],
                $c
            );
        }

        // 4. SEED PRODUCTS
        $products = [
            [
                'id' => 5,
                'brand_id' => 1,
                'name' => 'Son Thỏi 3CE Blur Matte Lipstick',
                'slug' => 'son-thoi-3ce-blur-matte-lipstick',
                'sku' => '3CE-BLUR-MATTE',
                'original_price' => 335000.00,
                'description' => "Son thỏi 3CE Blur Matte Lipstick là sự kết hợp hoàn hảo giữa thiết kế độc đáo và chất lượng sản phẩm. Với hình dáng thỏi độc đáo và màu sắc đa dạng, sản phẩm mang đến sự lựa chọn phù hợp cho mọi phong cách trang điểm.\nSon có kết cấu mềm mịn, giúp đôi môi trở nên mềm mại và mịn màng như nhung. Chất son lướt nhẹ trên môi, tạo lớp nền môi nhẹ thoáng, mịn lì, cảm giác như không đánh son.\nVới công thức đặc biệt từ bột đàn hồi silicon, son bám màu trung bình từ 3-5 tiếng, giúp màu son lên môi chuẩn và lâu trôi hơn. Son cũng giúp che đi khuyết điểm môi một cách tự nhiên, không làm lộ vân môi và gây khô môi.\nThiết kế hình thỏi Parabol độc đáo mang đến vẻ ngoài trẻ trung và sang trọng cho sản phẩm. Với 10 màu sắc khác nhau, từ các tông hồng, cam đến tím, giúp bạn thể hiện nhiều phong cách trang điểm và tạo điểm nhấn riêng cho đôi môi.\nHiện son thỏi 3CE Blur Matte Lipstick đang được phân phối tại LuxeryLips với 9 màu:\nApricot Filter: Màu cam san hô. Nếu tô nhẹ, sẽ có chút ánh hồng nude ngọt ngào. Màu son nhẹ nhàng, nữ tính và thanh lịch, phù hợp với nhiều phong cách.\nPeanut Beige: Màu cam nude. Chất son tone nude nhưng vẫn mượt mà và đều màu. Đây là màu son kén da, cần makeup nhẹ nhàng hoặc dùng làm màu base.\nPure Layer: Tone hồng san hô. Cần dưỡng môi để tránh tình trạng bị đọng son vào lòng môi. Màu son này khá kén da, nhưng chỉ cần makeup nhẹ nhàng bạn sẽ có một màu son trong trẻo, tiểu thư ngọt ngào.\nMisty Day: Sự pha trộn của Pure Layer với tông tím nude nhẹ, tạo nên tone màu hồng tím san hô hài hòa. Màu son không quá kén da, mang đến sắc tím nhẹ giúp tone san hô sáng và trẻ trung hơn.\nIn The Fog: Sắc hồng thiên ánh tím nude. Màu son kén da nhất trong bảng màu, cần dưỡng môi kỹ. Màu sắc hồng rõ rệt, tươi sáng và phù hợp làm màu base.\nRosiness: Tone hồng khô. Màu son tươi sáng và ít kén da. Thiên về tone hồng san hô, mang đến màu son tự nhiên.\nSalty Cinnamon: Màu cam quế. Nếu tô nhẹ, màu son sẽ thiên về cam hồng khô, nếu tô đậm, màu son sẽ rõ ánh cam nâu nude. Màu son này cũng khá kén da, cần makeup trước.\nNewtro Wave: Màu hồng dâu. Tone màu tôn da và dễ dùng nhất trong bảng màu. Màu son này rất xinh, nữ tính, có thể tô lòng môi hay toàn bộ môi đều đẹp.\nMauve Drizzle: Hồng tím san hô với Tone hồng rõ rệt hơn. Nhờ đó, màu son dễ dùng và tôn da hơn. Khi tô toàn môi, màu son sẽ rõ ánh hồng, đều và mượt trên môi.",
                'uses' => "Tạo hiệu ứng mờ ảo: Với công thức \"Breathing Matte\", son giúp làm mờ những nếp rãnh môi và tạo bề mặt môi phẳng như lớp thảm nhung, tạo hiệu ứng mờ ảo trên môi.\nChe đi khuyết điểm môi: Son giúp che đi những khuyết điểm trên môi một cách tự nhiên, không làm lộ vân môi và gây khô môi.\nĐôi môi mềm mại và mịn màng: Kết cấu mềm mịn của son giúp đôi môi luôn mềm mại, mịn màng như nhung.\nBám màu trung bình: Son có khả năng bám màu trung bình từ 3-5 tiếng, giúp màu son lên môi chuẩn và lâu trôi hơn.\nTạo hiệu ứng ombre viền lòng môi: Ruột son được làm vát chéo giúp dễ dàng tạo hiệu ứng ombre viền lòng môi, cho phép bạn tạo nhiều kiểu trang điểm môi khác nhau.",
                'ingredients' => "Dimethicone, Silica, Isoamyl Laurate, Synthetic Wax, Dimethicone/Vinyl Dimethicone Crosspolymer, Isoeicosane, Barium Sulfate, Polyglyceryl-2 Triisostearate, Diisostearyl Malate, Dimethicone Crosspolymer, Phytosteryl Macadamiate, Iron Oxides (CI 77491), Euphorbia Cerifera (Candelilla) Wax, Microcrystalline Wax, Titanium Dioxide (CI 77891), Polyethylene, Iron Oxides (CI 77492), Copernicia Cerifera (Carnauba) Wax, C10-18 Triglycerides, Sorbitan Isostearate, Diglyceryl Sebacate/Isopalmitate, Phenyl Trimethicone, Polyhydroxystearic Acid, Candelilla Wax Esters, Polyglyceryl-2 Diisostearate, Ethylhexyl Palmitate, Isopropyl Myristate, Isostearic Acid, Lecithin, Disteardimonium Hectorite, Polyglyceryl-3 Polyricinoleate, Magnesium Myristate, Triethyl Citrate, Tocopherol, Iron Oxides (CI 77499), Red 7 Lake (CI 15850).",
                'is_active' => true,
            ],
            [
                'id' => 7,
                'brand_id' => 1,
                'name' => 'Son Thỏi 3CE Cashmere Hug Lipstick',
                'slug' => 'son-thoi-3ce-cashmere-hug-lipstick',
                'sku' => '3CE-CASHMERE',
                'original_price' => 429000.00,
                'description' => "Son Thỏi 3CE Cashmere Hug Lipstick là dòng son thỏi lì cao cấp mới nhất từ thương hiệu 3CE. Đây là sản phẩm son môi được đánh giá cao bởi sự tinh tế trong thiết kế, cùng với chất son mượt mà và lâu trôi. Sản phẩm không chỉ giúp tạo màu sắc tươi tắn cho đôi môi, mà còn giữ cho môi luôn mềm mại, không bị khô hay lộ vân môi. Đặc biệt, bảng màu của 3CE Cashmere Hug Lipstick vô cùng đa dạng, từ những tông màu nude nhẹ nhàng, dịu dàng cho đến các sắc đỏ, hồng tươi tắn, phù hợp với mọi sở thích và phong cách.\n\nĐiểm nhấn của dòng son này chính là chất son lì mịn màng như nhung cashmere, mang lại cảm giác nhẹ nhàng khi sử dụng và không gây cảm giác bết dính. Sự bền màu cũng là một trong những yếu tố làm nên sức hút của sản phẩm này, giúp bạn tự tin suốt cả ngày dài mà không lo bị lem hay trôi màu.",
                'uses' => "Giúp môi tươi tắn và nổi bật: Với những tông màu đa dạng, từ những màu hồng nhẹ nhàng đến những tông đỏ quyến rũ, son giúp đôi môi trở nên tươi tắn và nổi bật, tạo điểm nhấn cho khuôn mặt ngay cả khi bạn không trang điểm nhiều.\nLên màu chuẩn và bền màu: Son lên màu rất chuẩn ngay từ lần thoa đầu tiên, mang lại độ bền màu lâu dài. Bạn không cần phải lo lắng về việc son bị trôi hay phai màu trong suốt cả ngày.\nĐa dạng phong cách trang điểm: Với bảng màu đa dạng, từ những màu nhẹ nhàng, tự nhiên đến các màu sắc nổi bật, sản phẩm có thể dễ dàng phối hợp với nhiều phong cách trang điểm khác nhau, từ trang điểm tự nhiên hàng ngày đến các buổi tiệc tối sang trọng.",
                'ingredients' => "Dimethicone, Hydrogenated Polyisobutene, Polyethylene, Octyldodecyl Neopentanoate, Silica, Isobutyl Acetate Sucrose, Trimethylsiloxy Silicate, Stearic Acid Heptanoate, Dimethicone Crosspolymer, (Dimethicone/Vinyltrimethylsiloxy Silicate) Crosspolymer, Stearic Acid Caprylate, Kaolin, Lauroyl Lysine, Tetra(t-Butylhydroxyhydrocinnamic Acid) Penterythritol, Paraffin, Silica Silylate, Tocopherol Acetate, Microcrystalline Wax, Fragrance, Synthetic Wax, Sodium Acetyl Hyaluronate, Tocopherol, [+/-] Iron Oxides, Titanium Dioxide, Synthetic Fluorophlogopite, Red 230(1), Yellow 5, Red 202, Yellow 4, Red 104(1), Aluminum Hydroxide, Blue 1.",
                'is_active' => true,
            ],
            [
                'id' => 8,
                'brand_id' => 4,
                'name' => 'Son Kem Romand Blur Fudge Tint',
                'slug' => 'son-kem-romand-blur-fudge-tint',
                'sku' => 'BLUR-TINT',
                'original_price' => 159000.00,
                'description' => "Nhắc đến dòng son kem lì của Romand, không thể không nhắc đến Son Kem Lì Romand Blur Fudge Tint.\n\nSon Kem Lì Romand Blur Fudge Tint sở hữu chất son matte mịn lì, vô cùng nhẹ môi, tạo cảm giác thoải mái khi sử dụng. Thêm vào đó, sản phẩm còn vô cùng dễ tán, đem lại lớp trang điểm môi mướt mịn chỉ sau một lần lướt cọ. Mùi thơm dịu nhẹ là một trong những điểm cộng của bộ sưu tập lần này.\n\nRomand Blur Fudge Tint sở hữu chất son chứa hệ sắc tố cao, có khả năng tùy chỉnh màu sắc linh hoạt. Bạn có thể thoải mái tô chồng nhiều lớp son lên mà không cảm thấy nặng môi, mỗi một lớp son lại đem lại phong cách khác nhau cho người sử dụng. Son có khả năng khô nhanh, tiệp đều trên môi nhanh chóng và để lại lớp basi mịn lì, bền màu suốt cả ngày dài. \n\nBộ sưu tập Romand Blur Fudge Tint gồm nhiều màu sắc khác nhau, thể hiện nét quyến rũ, thời thượng, thu hút mọi giới.",
                'uses' => "-Tạo màu môi: Tạo màu sắc rạng rỡ, tươi tắn, mang lại sức sống cho đôi môi.\n- Che phủ khuyết điểm môi: Son giúp che đi đôi môi nhợt nhạt, thiếu sức sống.\n- Hỗ trợ hoàn thiện lớp make-up: Góp phần làm hoàn thiện hơn lớp make-up look, tạo điểm nhấn thu hút mọi ánh nhìn.\n- Duy trì màu môi đẹp: Giữ màu môi bền đẹp suốt nhiều giờ liền.",
                'ingredients' => "Dimethicone, Dimethicone Crosspolymer, Tribehenin, Diisostearyl Malate, Polyglyceryl-2 Triisostearate, Titanium Dioxide (Ci 77891), Vinyl Dimethicone/Methicone Silsesquioxane Crosspolymer, Yellow Iron Oxide (Ci 77492), Macadamia Ternifolia Seed Oil, Cetyl PEG/PPG-10/1 Dimethicone, Sorbitan Isostearate, VP/Hexadecene Copolymer, Paraffin, Polyglyceryl-2 Diisostearate, Microcrystalline Wax, Triethoxycaprylylsilane, Tocopherol, Dehydroacetic Acid, Benzyl Benzoate, Fragrance, Anise Alcohol, Yellow No. 6 (Ci 15985), Red Iron Oxide (Ci 77491), Mica (Ci 77019), Red No. 6 (Ci 15850), Black Iron Oxide (Ci 77499), Red No. 33 (Ci 17200), Yellow No. 5 (Ci 19140), Red No. 27 (Ci 45410), Blue No. 1 (Ci 42090), Red No. 22 (Ci 45380).",
                'is_active' => true,
            ],
            [
                'id' => 9,
                'brand_id' => 4,
                'name' => 'Son Tint Romand Juicy Lasting Tint',
                'slug' => 'son-tint-romand-juicy-lasting-tint',
                'sku' => 'ROMAND-JUICY-TINT',
                'original_price' => 199000.00,
                'description' => "Son Bóng Romand Juicy Lasting Tint nổi bật với kết cấu son tint bóng, lên màu chuẩn đẹp chỉ sau vài lần lướt cọ. Chất son bóng trong trẻo tựa như lớp đường kẹo hồ lô, có độ bóng vừa phải, tạo cảm giác tươi mát, ngọt ngào, không hề gây dính hay khó chịu cho môi. Thêm vào đó, son còn có khả năng giữ màu lâu trôi khá tốt, duy trì màu sắc chân thật của son trên môi trong nhiều giờ liền.\nRomand Juicy Lasting Tint có thiết kế trụ tròn mảnh mai, dễ cầm nắm. Thân son được “áo” một lớp màu trùng với màu son bên trong, giúp bạn dễ dàng hơn trong việc lựa chọn màu son. Sản phẩm có đầu cọ thiết kế vát nhẹ, mềm mại, giúp lấy son dễ dàng hơn và lướt trên môi mượt mà, viền môi chuẩn. Son Bóng Romand Juicy Lasting Tint gồm những màu son tươi tắn, mang màu sắc của trái cây, tựa như ly nước soda mát lạnh, rất thích hợp dùng vào mùa hè.",
                'uses' => "Tạo màu môi: Tạo màu sắc tươi tắn, sáng bóng “tựa viên kẹo hồ lô” cho đôi môi, giữ môi luôn có “hồn”, bừng sáng, rạng ngời sức sống.",
                'ingredients' => "Water, Octyldodecanol, Dimethicone, Denatured Alcohol, Acrylates Copolymer, Sorbitan Stearate, Ethylcellulose, Behenyl Alcohol, Pentylene Glycol, Actinidia Chinensis (Kiwi) Fruit Extract, Carica Papaya (Papaya) Fruit Extract, Citrus Paradisi (Grapefruit) Fruit Extract, Cocos Nucifera (Coconut) Fruit Extract, Mangifera Indica (Mango) Fruit Extract, Psidium Guajava Fruit Extract, Polysorbate 60, Silica, Sodium Acrylate/Sodium Acryloyldimethyl Taurate Copolymer, Polyglyceryl-2 Triisostearate, Isohexadecane, Diisostearyl Malate, Polysorbate 80, Sorbitan Oleate, Ethylhexylglycerin, Polyglyceryl-2 Diisostearate, Butylene Glycol, Disodium EDTA, Phenoxyethanol, Fragrance, Benzyl Alcohol, Linalool, Yellow No. 6 (Ci 15985), Titanium Dioxide (Ci 77891), Red No. 6 (Ci 15850), Red No. 7 (Ci 15850:1), Yellow No. 5 (Ci 19140), Red Iron Oxide (Ci 77491), New Coccine (Ci 16255), Red No. 33 (Ci 17200), Red No. 28 (Ci 45410), Blue No. 1 (Ci 42090), Black Iron Oxide (Ci 77499), Yellow Iron Oxide (Ci 77492).",
                'is_active' => true,
            ],
            [
                'id' => 10,
                'brand_id' => 4,
                'name' => 'Son Kem Romand Blur Fudge',
                'slug' => 'son-kem-romand-blur-fudge',
                'sku' => 'ROMAND-BLUR-FUDGE',
                'original_price' => 195000.00,
                'description' => "Son Kem Lì Romand Blur Fudge Tint sở hữu chất son matte mịn lì, vô cùng nhẹ môi, tạo cảm giác thoải mái khi sử dụng. Thêm vào đó, sản phẩm còn vô cùng dễ tán, đem lại lớp trang điểm môi mướt mịn chỉ sau một lần lướt cọ. Mùi thơm dịu nhẹ là một trong những điểm cộng của bộ sưu tập lần này.\nRomand Blur Fudge Tint sở hữu chất son chứa hệ sắc tố cao, có khả năng tùy chỉnh màu sắc linh hoạt. Bạn có thể thoải mái tô chồng nhiều lớp son lên mà không cảm thấy nặng môi, mỗi một lớp son lại đem lại phong cách khác nhau cho người sử dụng. Son có khả năng khô nhanh, tiệp đều trên môi nhanh chóng và để lại lớp basi mịn lì, bền màu suốt cả ngày dài.",
                'uses' => "- Tạo màu môi: Tạo màu sắc rạng rỡ, tươi tắn, mang lại sức sống cho đôi môi.\n- Che phủ khuyết điểm môi: Son giúp che đi đôi môi nhợt nhạt, thiếu sức sống.\n- Hỗ trợ hoàn thiện lớp make-up: Góp phần làm hoàn thiện hơn lớp make-up look, tạo điểm nhấn thu hút mọi ánh nhìn.\n- Duy trì màu môi đẹp: Giữ màu môi bền đẹp suốt nhiều giờ liền.",
                'ingredients' => "Dimethicone, Dimethicone Crosspolymer, Tribehenin, Diisostearyl Malate, Polyglyceryl-2 Triisostearate, Titanium Dioxide (Ci 77891), Vinyl Dimethicone/Methicone Silsesquioxane Crosspolymer, Yellow Iron Oxide (Ci 77492), Macadamia Ternifolia Seed Oil, Cetyl PEG/PPG-10/1 Dimethicone, Sorbitan Isostearate, VP/Hexadecene Copolymer, Paraffin, Polyglyceryl-2 Diisostearate, Microcrystalline Wax, Triethoxycaprylylsilane, Tocopherol, Dehydroacetic Acid, Benzyl Benzoate, Fragrance, Anise Alcohol, Yellow No. 6 (Ci 15985), Red Iron Oxide (Ci 77491), Mica (Ci 77019), Red No. 6 (Ci 15850), Black Iron Oxide (Ci 77499), Red No. 33 (Ci 17200), Yellow No. 5 (Ci 19140), Red No. 27 (Ci 45410), Blue No. 1 (Ci 42090), Red No. 22 (Ci 45380).",
                'is_active' => true,
            ],
            [
                'id' => 11,
                'brand_id' => 5,
                'name' => 'Son Tint Peripera Water Bare Tint',
                'slug' => 'son-tint-peripera-water-bare-tint',
                'sku' => 'PERIPERA-WATER-BARE',
                'original_price' => 211000.00,
                'description' => "Son Tint Peripera Water Bare Tint là một lựa chọn tuyệt vời cho những ai yêu thích sự tự nhiên và tươi mới cho đôi môi. Với công thức đặc biệt, em son này không chỉ mang lại hiệu ứng bóng nhẹ mà còn dưỡng ẩm cho đôi môi, giúp chúng luôn mềm mại và căng mọng.\nBảng màu Hip Gray của Peripera là sự kết hợp độc đáo của các tông màu hồng-tím lạnh, tạo nên vẻ đẹp hiện đại và cá tính.\nĐặc biệt, Son Tint Peripera Water Bare Tint nổi bật với thiết kế đầu cọ hình trái tim, giúp bạn dễ dàng tạo hình dạng môi trái tim ưa thích. Độ bám màu của dòng son này khá tốt và không gây cảm giác khô môi sau khi sử dụng.\n\nVới thành phần chứa phức hợp tinh dầu Jojoba và hạt mơ, son Peripera Water Bare Tint còn cung cấp độ ẩm cao và ngăn ngừa tình trạng môi khô, bong tróc, mang lại cảm giác mềm mại và khỏe khoắn cho đôi môi suốt cả ngày dài.\nNgoài ra, finish glossy mềm mượt của sản phẩm khiến cho đôi môi trông tự nhiên và tươi mới, đồng thời lớp tint nhẹ nhàng sau khi son phai đi vẫn giữ được màu sắc nhẹ nhàng và cuốn hút.",
                'uses' => "Dưỡng ẩm đặc biệt: Son Peripera Water Bare Tint chứa phức hợp tinh dầu Jojoba và hạt mơ, cung cấp độ ẩm cao giúp làn môi mềm mại và căng bóng suốt cả ngày.\nHiệu ứng bóng nhẹ tự nhiên: Với finish glossy mềm mượt, sản phẩm mang lại cho đôi môi vẻ đẹp tự nhiên và tươi mới, không gây cảm giác nặng nề.",
                'ingredients' => "Purified Water, Diisostearyl Maleate, Bis-Diglyceryl Polyacyl Adipate-2, Diphenylsiloxyphenyl Trimethicone, Diphenyl Dimethicone, Hydrogenated Polyisobutene, Butylene Glycol, Glycerin, 1,2-Hexanediol, Ceteareth-20, Cetearyl Alcohol, Cetyl PEG/PPG-10/1 Dimethicone, Hydroxyethyl Acrylate/Sodium Acryloyl Dimethyl Taurate Copolymer, Polysorbate 60, Phenoxyethanol, Titanium Dioxide, Squalane, Ethylhexylglycerin, Yellow No. 4, Yellow No. 5, Red No. 104 (1), Red No. 227, Blue No. 1, Fragrance, Disodium EDTA, Sorbitan Isostearate, Aluminum Hydroxide, Ammonium Polyacrylate, Argan Kernel Oil, Shea Butter, Camellia Seed Oil, Caprylic/Capric Triglyceride, Jojoba Seed Oil, Sweet Almond Oil, Apricot Seed Oil, Evening Primrose Oil, Olive Oil, Mango Extract, Rosehip Fruit Extract.",
                'is_active' => true,
            ],
            [
                'id' => 12,
                'brand_id' => 6,
                'name' => 'Son Thỏi Maybelline Ultimatte Mịn Môi Siêu Nhẹ',
                'slug' => 'son-thoi-maybelline-ultimatte-min-moi-sieu-nhe',
                'sku' => 'MAYBELLINE-ULTIMATTE',
                'original_price' => 228000.00,
                'description' => "Son Thỏi MAYBELLINE Ultimatte nổi bật với công nghệ làm mờ rãnh môi cho hiệu ứng son lên môi mịn mì, mờ ảo và nhẹ tênh. Chất son mềm mịn, không để lộ vân môi, giúp che khuyết điểm môi cực hiệu quả. Son Thỏi MAYBELLINE được bổ sung tinh dầu Jojoba giúp dưỡng ẩm sâu, mang đến đôi môi mềm mịn, không gây cảm giác khó chịu hay nứt nẻ cho đôi môi sau khi sử dụng.\n\nĐầu son có thiết kế hình giọt sương nhỏ giúp son dễ dàng lướt nhẹ trên môi, đảm bảo khả năng lên màu “chuẩn chỉnh” chỉ với 1 lần lướt. Sản phẩm có độ bền màu cao, giữ sắc môi luôn tươi tắn rạng rỡ suốt nhiều giờ liền. Phần vỏ son mảnh mai, thon gọn cùng màu sắc mắt cũng là điểm công mang đến phong cách sang trọng, thời thượng mà không kém phần trẻ trung.",
                'uses' => "Chất son mềm, mượt, mịn giúp che khuyết điểm môi hữu hiệu, tạo hiệu ứng mờ để không làm lộ vân môi.\nCấp ẩm vừa đủ, không gây cảm giác khó chịu hay nứt nẻ cho đôi môi.\nSon lên màu chuẩn với độ bền màu cao.\nTạo hiệu ứng mịn lì sang trọng, mang tới vẻ đẹp không tì vết cho đôi môi.",
                'ingredients' => "Dimethicone, Bis-Diglyceryl Polyacyladipate-2, Hydrogenated Polyisobutene, Phenyl Trimethicone, Tridecyl Trimellitate, Hydrogenated Jojoba Oil, Dimethicone Crosspolymer, Vinyl Dimethicone/Methicone Silsesquioxane Crosspolymer, Kaolin, Isostearyl Isostearate, Paraffin, Isohexadecane, Nylon-12, Cera Microcristallina / Microcrystalline Wax / Cire, Microcristalline, Polyethylene, Alumina, Synthetic Wax, Pentaerythrityl Tetra-Di-T-Butyl Hydroxyhydrocinnamate, Aluminum Hydroxide, Benzyl Alcohol, Silica, Tocopherol, Parfum / Fragrance…",
                'is_active' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['slug' => $p['slug']],
                $p
            );
        }

        // 5. SEED PRODUCT VARIANTS
        $variants = [
            // Product 5
            [
                'id' => 7,
                'sku' => '3CE-BLUR-MATTE-01',
                'product_id' => 5,
                'category_id' => 5,
                'type' => 'Rosiness',
                'price' => 449000.00,
                'color_description' => 'Tone hồng khô. Màu son tươi sáng và ít kén da. Thiên về tone hồng san hô, mang đến màu son tự nhiên.',
                'hex_color' => '#bb5b66',
                'stock' => 9,
                'image' => 'products/RJJPPLFJpMqmowxjIKnhL9vNe3RtnOzbANiP6Q7L.jpg',
            ],
            [
                'id' => 10,
                'sku' => '3CE-BLUR-MATTE-02',
                'product_id' => 5,
                'category_id' => 5,
                'type' => 'In The Fog',
                'price' => 335000.00,
                'color_description' => 'Sắc hồng thiên ánh tím nude. Màu son kén da nhất trong bảng màu, cần dưỡng môi kỹ. Màu sắc hồng rõ rệt, tươi sáng và phù hợp làm màu base.',
                'hex_color' => '#bb7575',
                'stock' => 3,
                'image' => 'products/NSbjsr0VM21ylGEEMdTVZLg7upRls3TEDj5WHoUS.jpg',
            ],
            [
                'id' => 11,
                'sku' => '3CE-BLUR-MATTE-03',
                'product_id' => 5,
                'category_id' => 5,
                'type' => 'Pure Layer',
                'price' => 449000.00,
                'color_description' => 'Tone hồng san hô. Cần dưỡng môi để tránh tình trạng bị đọng son vào lòng môi. Màu son này khá kén da, nhưng chỉ cần makeup nhẹ nhàng bạn sẽ có một màu son trong trẻo, tiểu thư ngọt ngào.',
                'hex_color' => '#cf575b',
                'stock' => 2,
                'image' => 'products/YTbfq09f76L6liNj8gR5khxOtfTXpSw6db3MIPrR.jpg',
            ],
            [
                'id' => 12,
                'sku' => '3CE-BLUR-MATTE-04',
                'product_id' => 5,
                'category_id' => 5,
                'type' => 'Salty Cinnamon',
                'price' => 449000.00,
                'color_description' => 'Màu cam quế. Nếu tô nhẹ, màu son sẽ thiên về cam hồng khô, nếu tô đậm, màu son sẽ rõ ánh cam nâu nude. Màu son này cũng khá kén da, cần makeup trước.',
                'hex_color' => '#874b4a',
                'stock' => 4,
                'image' => 'products/ureZmqkT52F5Yj35qcWi9enySXblLQ47CZYta61j.jpg',
            ],

            // Product 7
            [
                'id' => 13,
                'sku' => '3CE-CASHMERE-01',
                'product_id' => 7,
                'category_id' => 5,
                'type' => '01 Oat',
                'price' => 429000.00,
                'color_description' => 'Màu be Yến Mạch',
                'hex_color' => '#c86251',
                'stock' => 3,
                'image' => 'products/6v81xiB9DloyG6ydXn9pqbiklmMgljLMdEsYftuM.jpg',
            ],
            [
                'id' => 14,
                'sku' => '3CE-CASHMERE-02',
                'product_id' => 7,
                'category_id' => 5,
                'type' => '02 Pure Blending',
                'price' => 429000.00,
                'color_description' => 'Màu hồng da',
                'hex_color' => '#d16a7d',
                'stock' => 5,
                'image' => 'products/Pbaw5zIpUV2DJWLwGHSk9IkI4DFblZZtwFKCq0Yr.jpg',
            ],
            [
                'id' => 15,
                'sku' => '3CE-CASHMERE-03',
                'product_id' => 7,
                'category_id' => 5,
                'type' => '05 Cozy Whisper',
                'price' => 429000.00,
                'color_description' => 'Màu hồng hạ phai',
                'hex_color' => '#b63049',
                'stock' => 4,
                'image' => 'products/I9R1I5DSjBPdu7jNwjEDjlDSfChJSqrgMhJraXjE.jpg',
            ],

            // Product 8
            [
                'id' => 16,
                'sku' => 'BLUR-TINT-01',
                'product_id' => 8,
                'category_id' => 12,
                'type' => '16 Burnt Orange',
                'price' => 159000.00,
                'color_description' => 'Màu đỏ cam',
                'hex_color' => '#7d4335',
                'stock' => 6,
                'image' => 'products/LfgP5URZLB3JaVnDbSfOCoF2VDXFT31seqXgChAI.jpg',
            ],
            [
                'id' => 17,
                'sku' => 'BLUR-TINT-02',
                'product_id' => 8,
                'category_id' => 12,
                'type' => '02 Rosiental',
                'price' => 159000.00,
                'color_description' => 'Màu hoa hồng MLBB trầm',
                'hex_color' => '#af4d4c',
                'stock' => 4,
                'image' => 'products/8K2GYI0o80rAruygASshMRrtodyUqAor1n5ipNCw.jpg',
            ],

            // Product 9
            [
                'id' => 18,
                'sku' => 'ROMAND-JUICY-18',
                'product_id' => 9,
                'category_id' => 13,
                'type' => 'Jujube: Màu đỏ đất MLBB thanh lịch',
                'price' => 199000.00,
                'color_description' => 'Màu đỏ đất MLBB thanh lịch',
                'hex_color' => '#8a4046',
                'stock' => 14,
                'image' => 'products/RhzjRj4rcftWFryKcFVJE9f6VtkmuT5wsr68BED0.jpg',
            ],
            [
                'id' => 19,
                'sku' => 'ROMAND-JUICY-19',
                'product_id' => 9,
                'category_id' => 13,
                'type' => 'Nucadamia - Màu Đỏ Nâu Nude',
                'price' => 215000.00,
                'color_description' => 'Màu Đỏ Nâu Nude: Thích hợp với phong cách makeup tự nhiên, không hề kén da.',
                'hex_color' => '#924046',
                'stock' => 26,
                'image' => 'products/tvu7FffTANrTLK0u45BxaUBRZNlSvR84WpRwdeAT.jpg',
            ],
            [
                'id' => 20,
                'sku' => 'ROMAND-JUICY-20',
                'product_id' => 9,
                'category_id' => 13,
                'type' => 'Plomelo Skin - Màu cam hồng tự nhiên',
                'price' => 199000.00,
                'color_description' => 'Màu cam hồng tự nhiên, lấy cảm hứng từ quả bưởi, pha trộn giữa cam và hồng.',
                'hex_color' => '#924046',
                'stock' => 10,
                'image' => 'products/jW6H1vG5rdqYVs6AuC74EeWsPYd7v1SSspczPzlS.jpg',
            ],

            // Product 10
            [
                'id' => 21,
                'sku' => 'ROMAND-FUDGE-21',
                'product_id' => 10,
                'category_id' => 12,
                'type' => 'Cool Rose Up: Màu hoa hồng mận sang trọng',
                'price' => 195000.00,
                'color_description' => 'Cool Rose Up: Màu hoa hồng mận sang trọng',
                'hex_color' => '#b25164',
                'stock' => 5,
                'image' => 'products/8UqLdYmf6b0FWJqyFlNtE2J1Qd5jySDeNHzKYwZn.jpg',
            ],
            [
                'id' => 22,
                'sku' => 'ROMAND-FUDGE-22',
                'product_id' => 10,
                'category_id' => 12,
                'type' => 'Musky: Màu cam gạch',
                'price' => 195000.00,
                'color_description' => 'Musky: Màu cam gạch trendy',
                'hex_color' => '#ce5b5e',
                'stock' => 8,
                'image' => 'products/37LEci0uU5BCkgrtenmRB7LCteIAqtz3dbQ4Bz19.jpg',
            ],

            // Product 11
            [
                'id' => 24,
                'sku' => 'PERIPERA-BARE-24',
                'product_id' => 11,
                'category_id' => 13,
                'type' => 'Son Tint Peripera Water Bare Tint 3.7g Màu 11 Hip Rose',
                'price' => 211000.00,
                'color_description' => 'Son Tint Peripera Water Bare Tint 3.7g Màu 11 Hip Rose',
                'hex_color' => '#ca575e',
                'stock' => 15,
                'image' => 'products/q8sMIcwr6I8B7HmaTvs3EJ910oR86KL1JZ1osbXI.jpg',
            ],
            [
                'id' => 25,
                'sku' => 'PERIPERA-BARE-25',
                'product_id' => 11,
                'category_id' => 13,
                'type' => 'Son Tint Peripera Water Bare Tint 3.7g Màu 06 Softly Brown',
                'price' => 211000.00,
                'color_description' => 'Son Tint Peripera Water Bare Tint 3.7g Màu 06 Softly Brown',
                'hex_color' => '#cf5258',
                'stock' => 3,
                'image' => 'products/9GFeM0GuWsGyhAjcDlVpKXu0uuKhqU7OqLLvFuHz.jpg',
            ],

            // Product 12
            [
                'id' => 26,
                'sku' => 'MAYBELLINE-ULTI-26',
                'product_id' => 12,
                'category_id' => 5,
                'type' => 'More Rust: Đỏ gạch.',
                'price' => 228000.00,
                'color_description' => 'More Rust: Đỏ gạch.',
                'hex_color' => '#ad1715',
                'stock' => 10,
                'image' => 'products/bVnBja0dqJJSHxF02B73Jq63x0zCcwM2l67hhG2r.jpg',
            ],
        ];

        foreach ($variants as $v) {
            ProductVariant::updateOrCreate(
                ['product_id' => $v['product_id'], 'type' => $v['type']],
                $v
            );
        }
    }
}
