<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Subject;
use App\Models\Simulator;
use App\Models\SimulatorGrade;


class SimulatorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $simulators = [
            [
                "name_simulator"=>"Bài 2: Nam hay nữ? (2 tiết)",
                "related_lesson"=>"Hệ sinh sản nữ",
                "user_guide"=>"",
                "url_simulator"=>"simulators/female-reproductive-system",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 2: Nam hay nữ? (2 tiết)",
                "related_lesson"=>"Hệ sinh sản nam",
                "user_guide"=>"",
                "url_simulator"=>"simulators/male-reproductive-system",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 3: Cơ thể chúng ta được hình thành như thế nào?",
                "related_lesson"=>"9 tháng để sinh",
                "user_guide"=>"",
                "url_simulator"=>"simulators/9-months-to-create-life",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 3: Cơ thể chúng ta được hình thành như thế nào?",
                "related_lesson"=>"Chu kỳ kinh nguyệt",
                "user_guide"=>"",
                "url_simulator"=>"simulators/menstrual-cycle",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 14: Phòng tránh HIV/ AIDS",
                "related_lesson"=>"Vòng đời của HIV",
                "user_guide"=>"",
                "url_simulator"=>"simulators/the-life-cycle-of-hiv",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 15: Thái độ đối với người nhiễm HIV/AIDS",
                "related_lesson"=>"Vòng đời của HIV",
                "user_guide"=>"",
                "url_simulator"=>"simulators/the-life-cycle-of-hiv",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 43: Cơ quan sinh sản của thực vật có hoa",
                "related_lesson"=>"Sinh sản hữu tính ở thực vật có hoa",
                "user_guide"=>"",
                "url_simulator"=>"simulators/sexual-reproduction-in-angiosperms",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 44: Sự sinh sản của thực vật có hoa",
                "related_lesson"=>"Nảy mầm",
                "user_guide"=>"",
                "url_simulator"=>"simulators/germination",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 44: Sự sinh sản của thực vật có hoa",
                "related_lesson"=>"Sinh sản hữu tính ở thực vật có hoa",
                "user_guide"=>"",
                "url_simulator"=>"simulators/sexual-reproduction-in-angiosperms",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 44: Sự sinh sản của thực vật có hoa",
                "related_lesson"=>"Lai thuần chủng và lai chéo",
                "user_guide"=>"",
                "url_simulator"=>"simulators/inbreeding-and-crossbreeding",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 45: Cây con mọc lên từ hạt",
                "related_lesson"=>"Hạt",
                "user_guide"=>"",
                "url_simulator"=>"simulators/the-seed",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 45: Cây con mọc lên từ hạt",
                "related_lesson"=>"Giải phẫu hạt",
                "user_guide"=>"",
                "url_simulator"=>"simulators/seed-anatomy",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 46: Cây con có thể mọc lên từ một số bộ phận của cây mẹ",
                "related_lesson"=>"Hoa",
                "user_guide"=>"",
                "url_simulator"=>"simulators/the-flower",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 46: Cây con có thể mọc lên từ một số bộ phận của cây mẹ",
                "related_lesson"=>"Vòng đời của cây",
                "user_guide"=>"",
                "url_simulator"=>"simulators/plant-lì-cycle",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 46: Cây con có thể mọc lên từ một số bộ phận của cây mẹ",
                "related_lesson"=>"Nảy mầm",
                "user_guide"=>"",
                "url_simulator"=>"simulators/germination",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 47: Sự sinh sản của động vật",
                "related_lesson"=>"Sinh sản hữu tính ở động vật",
                "user_guide"=>"",
                "url_simulator"=>"simulators/sexual-reproduction-in-animals",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 48: Sự sinh sản của côn trùng",
                "related_lesson"=>"Biến thái ở bướm",
                "user_guide"=>"",
                "url_simulator"=>"simulators/metamorphosis-in-the-butterfly",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 48: Sự sinh sản của côn trùng",
                "related_lesson"=>"Hô hấp ở côn trùng",
                "user_guide"=>"",
                "url_simulator"=>"simulators/insect-respiration",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 49: Sự sinh sản của ếch",
                "related_lesson"=>"Vòng đời của ếch",
                "user_guide"=>"",
                "url_simulator"=>"simulators/frog-life-cycle",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 49: Sự sinh sản của ếch",
                "related_lesson"=>"Biến thái của nòng nọc",
                "user_guide"=>"",
                "url_simulator"=>"simulators/metamorphosis-of-tadpole",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 50: Sự sinh sản và nuôi con của chim",
                "related_lesson"=>"Từ trứng thành gà",
                "user_guide"=>"",
                "url_simulator"=>"simulators/from-egg-to-chick",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 50: Sự sinh sản và nuôi con của chim",
                "related_lesson"=>"Mỏ chim",
                "user_guide"=>"",
                "url_simulator"=>"simulators/birds-beaks",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 51: Sự sinh sản của thú",
                "related_lesson"=>"Sinh sản hữu tính ở động vật",
                "user_guide"=>"",
                "url_simulator"=>"simulators/sexual-reproduction-in-animals",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 53: Ôn tập thực vật và động vật",
                "related_lesson"=>"Vòng đời của cá hồi",
                "user_guide"=>"",
                "url_simulator"=>"simulators/life-cycle-of-a-trout",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 56: Vai trò của môi trường tự nhiên đối với đời sống con người",
                "related_lesson"=>"Chu trình cacbon",
                "user_guide"=>"",
                "url_simulator"=>"simulators/carbon-cycle",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 57: Tác động của con người đến môi trường rừng",
                "related_lesson"=>"Xói mòn và phá rừng",
                "user_guide"=>"",
                "url_simulator"=>"simulators/erosion-and-deforestation",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bài 61: Ôn tập môi trường và tài nguyên thiên nhiên",
                "related_lesson"=>"Hiệu ứng nhà kính",
                "user_guide"=>"",
                "url_simulator"=>"simulators/greenhouse-effect",
                "subject"=>Subject::where('name','like','Khoa học')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Nguyên tử",
                "related_lesson"=>"Nguyên tử",
                "user_guide"=>"",
                "url_simulator"=>" simulators/the-atom",
                "subject"=>Subject::where('name','like','Hóa học')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Phân tử",
                "related_lesson"=>"Phân tử",
                "user_guide"=>"",
                "url_simulator"=>"simulators/molecules",
                "subject"=>Subject::where('name','like','Hóa học')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Đơn chất và hợp chất. Phân tử",
                "related_lesson"=>"Nguyên tử - Ion - Phân tử",
                "user_guide"=>"",
                "url_simulator"=>"simulators/atoms-ions-and-molecules",
                "subject"=>Subject::where('name','like','Hóa học')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Bài thực hành 3",
                "related_lesson"=>"Phân loại tuần hoàn các nguyên tố hóa học",
                "user_guide"=>"",
                "url_simulator"=>"simulators/periodical-classification-of-elements",
                "subject"=>Subject::where('name','like','Hóa học')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Công thức hóa học",
                "related_lesson"=>"Phản ứng hóa học",
                "user_guide"=>"",
                "url_simulator"=>"simulators/chemical-reaction",
                "subject"=>Subject::where('name','like','Hóa học')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Mol",
                "related_lesson"=>"Mol",
                "user_guide"=>"",
                "url_simulator"=>"simulators/molar-mass",
                "subject"=>Subject::where('name','like','Hóa học')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Tính chất của ôxi",
                "related_lesson"=>"CO2",
                "user_guide"=>"",
                "url_simulator"=>"simulators/co2",
                "subject"=>Subject::where('name','like','Hóa học')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Không khí - sự cháy",
                "related_lesson"=>"Sự cháy",
                "user_guide"=>"",
                "url_simulator"=>"simulators/combustion",
                "subject"=>Subject::where('name','like','Hóa học')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Nước",
                "related_lesson"=>"Nước",
                "user_guide"=>"",
                "url_simulator"=>"simulators/h2o",
                "subject"=>Subject::where('name','like','Hóa học')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"A xit – Baz ơ – Muối",
                "related_lesson"=>"Axit - Bazo",
                "user_guide"=>"",
                "url_simulator"=>"simulators/acid-base-titration",
                "subject"=>Subject::where('name','like','Hóa học')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Phân số thập phân",
                "related_lesson"=>"Số thập phân và phân số",
                "user_guide"=>"",
                "url_simulator"=>"simulators/deciaml-and-fraction-numbers",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Ôn tập bảng đơn vị đo độ dài",
                "related_lesson"=>"Đo chiều dài",
                "user_guide"=>"",
                "url_simulator"=>"simulators/measuring-lengths",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Hình tam giác",
                "related_lesson"=>"Tam giác",
                "user_guide"=>"",
                "url_simulator"=>"simulators/triangle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Hình tròn – Đường tròn",
                "related_lesson"=>"Tìm tâm của hình tròn",
                "user_guide"=>"",
                "url_simulator"=>"simulators/finding-the-center-of-circle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Hình hộp chữ nhật-Hình lập phương",
                "related_lesson"=>"Các mặt của hình lập phương",
                "user_guide"=>"",
                "url_simulator"=>"simulators/nets-of-a-cube",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Thể tích của một hình",
                "related_lesson"=>"Thể tích của các hình khối",
                "user_guide"=>"",
                "url_simulator"=>"simulators/volumn-of-simple-solids",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Xăng-ti-mét khối ; Đề-xi-mét khối",
                "related_lesson"=>"Thể tích của các hình khối",
                "user_guide"=>"",
                "url_simulator"=>"simulators/volumn-of-simple-solids",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Bảng đơn vị đo thời gian",
                "related_lesson"=>"Đo thời gian",
                "user_guide"=>"",
                "url_simulator"=>"simulators/measuring-time",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Góc nội tiếp",
                "related_lesson"=>"Góc nội tiếp",
                "user_guide"=>"",
                "url_simulator"=>"simulators/inscribed-angle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Đường tròn ngoại tiếp. Đường tròn nội tiếp.",
                "related_lesson"=>"Đường tròn ngoại tiếp",
                "user_guide"=>"",
                "url_simulator"=>"simulators/circumscribed-circle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Độ dài đường tròn, cung tròn",
                "related_lesson"=>"Đường tròn nội tiếp",
                "user_guide"=>"",
                "url_simulator"=>"simulators/incircle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Diện tích hình tròn, hình quạt tròn.",
                "related_lesson"=>"Đường tròn ngoại tiếp",
                "user_guide"=>"",
                "url_simulator"=>"simulators/circumscribed-circle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Toán lớp 5 - Hình tam giác",
                "related_lesson"=>"Tam giác",
                "user_guide"=>"",
                "url_simulator"=>"simulators/triangle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Toán lớp 5 - Hình tròn. Đường tròn",
                "related_lesson"=>"Tìm tâm của đường tròn",
                "user_guide"=>"",
                "url_simulator"=>"simulators/finding-center-of-a-circle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Toán lớp 5 Ôn tập: Bảng đơn vị đo độ dài",
                "related_lesson"=>"Đo chiều dài",
                "user_guide"=>"",
                "url_simulator"=>"simulators/measuring-lengths",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Toán lóp 5 Thể tích của một hình",
                "related_lesson"=>"Thể tích của các hình khối đơn giản",
                "user_guide"=>"",
                "url_simulator"=>"simulators/volumn-of-simple-solids",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Toán lớp 5: Thời gian",
                "related_lesson"=>"Đo thời gian",
                "user_guide"=>"",
                "url_simulator"=>"simulators/measuring-time",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Toán 5 Hình lập phương",
                "related_lesson"=>"Các mặt của hình lập phương",
                "user_guide"=>"",
                "url_simulator"=>"simulators/nets-of-a-cube",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['5']
            ],
            [
                "name_simulator"=>"Toán lớp 9 - Bài 8 - Đường tròn ngoại tiếp và đường tròn nội tiếp",
                "related_lesson"=>"Đường tròn ngoại tiếp",
                "user_guide"=>"",
                "url_simulator"=>"simulators/circumscribed-circle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['9']
            ],
            [
                "name_simulator"=>"Toán lớp 9 - Bài 8 - Đường tròn ngoại tiếp và đường tròn nội tiếp",
                "related_lesson"=>"Đường tròn ngoại tiếp",
                "user_guide"=>"",
                "url_simulator"=>"simulators/circumscribed-circle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['9']
            ],
            [
                "name_simulator"=>"Toán học lớp 9 - Bài 8 - Đường tròn ngoại tiếp và đường tròn nội tiếp",
                "related_lesson"=>"Đường tròn nội tiếp",
                "user_guide"=>"",
                "url_simulator"=>"simulators/incircle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['9']
            ],
            [
                "name_simulator"=>"Toán 9 Bài 3: Góc nội tiếp",
                "related_lesson"=>"Góc nội tiếp",
                "user_guide"=>"",
                "url_simulator"=>"simulators/inscribed-angle",
                "subject"=>Subject::where('name','like','Toán')->first()->id,
                "grades"=>['9']
            ],
            [
                "name_simulator"=>"Công Nghệ 8 - Bài 29. Truyền chuyển động",
                "related_lesson"=>"Vặn nút chai",
                "user_guide"=>"Đồ vật kỹ thuật là một vật được thiết kế bởi con người để đáp ứng một hoặc nhiều nhu cầu.\nDanh sách các nhu cầu và ràng buộc cần tôn trọng là các đặc tả. Đặc tả là bước đầu tiên dẫn đến việc sản xuất một đồ vật kỹ thuật. Sơ đồ là một bản vẽ đơn giản hóa hiển thị các phần khác nhau của đồ vật và hoạt động của nó.\nCác phần khác nhau của một vật (phần) được kết nối bằng các liên kết có thể cố định hoặc di động, vĩnh viễn hoặc không vĩnh viễn.\nCác kiểu chuyển động chính là chuyển động tịnh tiến, chuyển động quay và cuộn xoắn (là sự kết hợp của hai kiểu đầu tiên). Chuyển động của một bộ phận di động thường bị hạn chế bởi một bộ phận dẫn.\nThiết bị truyền động chuyển đổi một kiểu chuyển động thành một kiểu khác. Đây là trường hợp của hệ thống bánh răng / thanh răng và bánh răng chuyển đổi chuyển động quay thành chuyển động tịnh tiến.\nĐể thực hiện chức năng của nó, một đồ vật kỹ thuật thường bao gồm các máy cơ đơn giản: mặt phẳng nghiêng, đòn bẩy, ròng rọc, v.v...\nNhấn vào thẻ 'Thao tác' để thao tác với cái vặn nút chai.\nCác điểm nóng của thẻ 'Thao tác':\nNhấn và kéo đòn bẩy để khởi động.\nNhấn và kéo tay cầm theo chiều ngang để quay.\nNhấn và kéo tay cầm lên xuống theo chiều dọc.",
                "url_simulator"=>"simulators/corkscrew",
                "subject"=>Subject::where('name','like','Công nghệ')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Công Nghệ 8 - Bài 29. Truyền chuyển động",
                "related_lesson"=>"Bánh răng và xích truyền",
                "user_guide"=>"Làm cách nào để truyền năng lượng quay từ bánh xe này sang bánh xe khác? Mô phỏng này minh họa hai khả năng:
                \nBánh răng
                \nXích truyền
                \nBánh răng là một hệ thống cơ học được tạo nên từ hai đĩa có răng đan khớp vào nhau. Hai bánh răng tiếp xúc với nhau mọi lúc khi một răng của bánh răng dẫn đẩy một răng của bánh răng bị dẫn. Bánh răng dẫn truyền chuyển động quay của nó sang bánh răng bị dẫn, nhưng các bánh răng quay ngược chiều nhau.
                \nXích truyền kết nối hai bánh răng không tiếp xúc. Không giống như dây đai, xích được tạo nên từ các liên kết cho phép truyền trơn tru mà không bị trượt. Các bánh răng quay cùng chiều.
                \nTrong cả hai trường hợp, tốc độ quay của bánh răng bị dẫn phụ thuộc vào kích thước của cả hai bánh răng.
                \nChọn số răng cho mỗi bánh răng.",
                "url_simulator"=>"simulators/gears-and-chain-transmission",
                "subject"=>Subject::where('name','like','Công nghệ')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Công Nghệ 8 - Bài 29. Truyền chuyển động",
                "related_lesson"=>"Bánh răng dị thường",
                "user_guide"=>"Được mang đến cho bạn nhờ sự hợp tác với Bảo tàng Nghệ thuật và Nghề nghiệp - Paris.
                \nMột hệ thống bánh răng liên quan đến việc truyền chuyển động. Hai bánh có răng đảm bảo một lực dẫn mà không bị trượt. Trong bốn hệ thống này, bánh dẫn (ở trên) quay ở tốc độ không đổi.                
                \nTrường hợp đầu tiên: hệ thống 'chữ thập Malta' biến đổi sự quay liên tục thành chuyển động không liên tục: đây là một bộ đếm lượt.
                \nCác trường hợp khác: tốc độ thay đổi của bánh xe dưới được xác định bởi hình dạng của bánh xe.",
                "url_simulator"=>"simulators/unusual-gears",
                "subject"=>Subject::where('name','like','Công nghệ')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Công Nghệ 8 - Bài 29. Truyền chuyển động",
                "related_lesson"=>"Hệ thống truyền động",
                "user_guide"=>"Các hệ thống truyền động cho phép truyền năng lượng cơ học từ vật này sang vật khác mà không làm thay đổi bản chất của chuyển động (quay sang quay hoặc tịnh tiến sang tịnh tiến). 
                \nVí dụ: Trong bánh răng, hoặc bánh xe ma sát, bánh phát động truyền chuyển động quay của nó đến bánh sau. Cả hai bánh đều quay.                
                \nSự truyền chuyển động có thể xảy ra do tiếp xúc trực tiếp giữa hai chi tiết cơ khí hoặc với sự trợ giúp của một thiết bị trung gian như xích hoặc đai.",
                "url_simulator"=>"simulators/motion-transmission-systems",
                "subject"=>Subject::where('name','like','Công nghệ')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Công Nghệ 8 - Bài 30. Truyền chuyển động",
                "related_lesson"=>"Hệ thống biến đổi chuyển động",
                "user_guide"=>"Các cơ chế biến đổi chuyển động chuyển năng lượng cơ học từ vật này sang vật khác trong khi sửa đổi bản chất của chuyển động (quay sang tịnh tiến hoặc tịnh tiến sang quay).",
                "url_simulator"=>"simulators/motion-transformation-systems",
                "subject"=>Subject::where('name','like','Công nghệ')->first()->id,
                "grades"=>['8']
            ],
            [
                "name_simulator"=>"Công Nghệ 8 - Bài 29. Truyền chuyển động",
                "related_lesson"=>"Đẩy chân - Bánh cao - Xe đạp",
                "user_guide"=>"Hoạt ảnh được mang đến cho bạn nhờ sự hợp tác với Bảo tàng Nghệ thuật và Nghề nghiệp - Paris. 
                \nXe đạp đẩy chân (nghĩa đen 'bàn chân nhanh') sử dụng một bộ xích với một đĩa xích cố định ở bánh trước: một vòng của bàn đạp tương đương với một vòng của bánh lái.                
                \nĐể di chuyển khoảng cách xa hơn với mỗi vòng của bàn đạp, cần phải tăng đường kính của bánh trước: Penny Farthing là một ứng dụng cực đoan của nguyên lý này.                
                \nXe đạp sử dụng một hệ thống truyền với một xích kết nối hai đĩa xích có kích thước khác nhau, cung cấp khoảng cách di chuyển lớn hơn với mỗi vòng của bàn đạp.",
                "url_simulator"=>"simulators/velocipede-high-wheel-bicycle",
                "subject"=>Subject::where('name','like','Công nghệ')->first()->id,
                "grades"=>['8']
            ],

        ];
        DB::beginTransaction();
        try{
            foreach($simulators as $simualtor){
                $ceatedSimulatorRecord=Simulator::create([
                    "name_simulator"=>$simualtor['name_simulator'],
                    "subject_id"=>$simualtor['subject'],
                    "related_lesson"=>$simualtor['related_lesson'],
                    "user_guide"=>$simualtor['user_guide'],
                    "url_simulator"=>$simualtor['url_simulator'],
                ]);
                \Log::debug(' ceatedSimulatorRecord -> id: '. $ceatedSimulatorRecord->id);

                foreach($simualtor['grades'] as $grade){
                    $simulatorGrade = SimulatorGrade::create([
                        "grade" => $grade,
                        "simulator_id" => $ceatedSimulatorRecord->id
                    ]);
                    \Log::debug(' simulatorGrade : '. print_r($simulatorGrade,true));

                }
            }
            DB::commit();
        }catch(Exception $e){
            \Log::debug('seeder error : '. $e);
            DB::rollBack();
        }
    }
}
