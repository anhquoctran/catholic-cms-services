<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 1/2/2018
 * Time: 19:56
 */
use App\Models\Member;

class SeederHelper
{
    public static function getNextUuid() {
        $lastMember = Member::where('is_deleted', '<>', IS_DELETED)->orderByDesc('id')->first();
        $uuid = $lastMember->uuid;
        $uuid = substr($uuid, 2);
        $number = (int) $uuid;
        $number++;
        $nextUuid = 'HV'.sprintf('%05d', $number);
        return $nextUuid;
    }

    public static function removeVietnameseCharacters($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        //$str = str_replace(” “, “-”, str_replace(“&*#39;”,”",$str));
        return $str;
    }

    /**
     * @param $lastname
     * @return int
     */
    public static function getGenderFromRandomName($lastname) {
        return ((!in_array($lastname, self::$femaleLastname)) ? 1 : 2);
    }

    public static function getFirstname($gender, $middlename) {
        if($gender == 1) {
            return self::$maleFirstname[array_rand(self::$maleFirstname)];
        } else {
            $firstname = self::$femaleFirstname[array_rand(self::$femaleFirstname)];

            if($firstname == $middlename) {
                return self::getFirstname(2, $middlename);
            } else {
                return $firstname;
            }
        }
    }

    /**
     * @var array
     */
    public static $maleFirstname = [
        "Anh",
        "Ánh",
        "Bình",
        "Cường",
        "Duy",
        "Dũng",
        "Đại",
        "Giang",
        "Hòa",
        "Hoàng",
        "Hiếu",
        "Huy",
        "Khiêm",
        "Quốc",
        "Thái",
        "Khoa",
        "Linh",
        "Minh",
        "Phương",
        "Qúy",
        "Rạng",
        "Xuân",
    ];

    public static $femaleFirstname = [
        "Anh",
        "Chi",
        "Châu",
        "Dung",
        "Trân",
        "Huyền",
        "Linh",
        "Lan",
        "Nhi",
        "Như",
        "Yến",
        "Uyên",
        "Phương",
        "Phượng",
        "Thu",
        "Thủy",
        "Thúy",
        "Ngọc",
        "Hằng",
        "Ngân",
        "Ngọc",
        "Nguyệt",
        "Nga",
        "Quyên",
        "Quỳnh",
        "Trâm",
        "Hân",
        "Vy",
        "Giang",
        "Thư",
    ];

    /**
     * @var array
     */
    public static $maleLastname = [
        "Nguyễn Văn",
        "Đỗ Văn",
        "Hồ Văn",
        "Trần Văn",
        "Võ Văn",
        "Phan Văn",
        "Châu Văn",
        "Phạm Văn",
        "Lê Văn"
    ];

    public static $femaleLastname = [
        "Nguyễn Thị",
        "Đỗ Thị",
        "Lê Thị",
        "Trần Thị",
        "Võ Thị",
        "Nguyễn Ngọc",
        "Nguyễn Đỗ",
        "Nguyễn Phan",
        "Nguyễn Phạm",
        "Lê Trần",
        "Nguyễn Phan",
        "Phan Thị",
        "Châu Thị",
        "Phạm Thị"
    ];

    public static $middleName = [
        "Diệu",
        "Ngọc",
        "Thùy",
        "Huyền",
        "Bảo",
        "Yến",
        "Diễm",
        "Như",
        "Quỳnh",
        "Phương",
        "Kiều",
        "Hương",
        "Bích",
        "Khánh",
        "Linh",
        "Nhật",
        "Thảo",
        "Hải",
        "Mỹ",
        "Thu",
        "Nhã",
        "Anh",
        "Thụy",
        "Bảo",
        "",
    ];

    public static $saint_name_male = [
        "Anrê",
        "Antôn",
        "Augustinô",
        "Ða-Minh",
        "Emmanuel",
        "Giacôbê",
        "Gioan",
        "Giuse",
        "Henricô",
        "Nicôla",
        "Phanxicô",
        "Phaolô",
        "Phêrô",
        "Vincentê",
        "Stêphanô"
    ];

    public static $saint_name_female = [
        "Anna",
        "Maria",
        "Catarina",
        'Clara',
        "Lucia",
        'Teresa',
        'Mary'
    ];
}