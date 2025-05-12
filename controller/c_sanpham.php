<?php
require_once "../model/m_sanpham.php";

class SanPhamController {
    private $model;

    public function __construct() {
        $this->model = new SanPhamModel();
    }
    public function getDanhSachSanPham() {
        $limit = 5;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $start = ($page - 1) * $limit;

        $keyword = $_GET['keyword'] ?? '';
        $status = $_GET['status'] ?? '';
        $masp = $_GET['masp'] ?? '';  
        $sort_gia = $_GET['sort_gia'] ?? '';
        $sort_so_luong = $_GET['sort_so_luong'] ?? '';

        $total = $this->model->getTotalProducts($keyword, $status, $masp);  // Thêm tham số masp
        $result = $this->model->getProductsByPage($start, $limit, $keyword, $status, $masp, $sort_gia, $sort_so_luong);  // Thêm tham số masp

        $ds_sp = [];
        while ($row = $result->fetch_assoc()) {
            $ds_sp[] = $row;
        }

        return [
            'ds_sp' => $ds_sp,
            'page' => $page,
            'total_pages' => ceil($total / $limit)
        ];
    }
    public function xoaSanPham($masp) {
        return $this->model->deleteProduct($masp);
    }
    public function themSanPham($data, $image) {
    $target_dir = "../media/image/Product_img/";
    $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
    echo $imageFileType;
    $newFileName = $data['masp'] . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;

    if (in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            $masp = $data['masp'];
            $tensp = $data['tensp'];
            $nsx = $data['nsx'];
            $phanloai = $data['phanloai'];
            $soluong = $data['soluong'];
            $giatien = $data['giatien'];
            $mota = $data['mota'];
            $baohanh = $data['baohanh'];
            $image_path = $target_file;
            echo "<script>alert('Thêm sản phẩm thành công!');</script>";
            return $this->model->addProduct($masp, $tensp, $nsx, $phanloai, $soluong, $giatien, $mota, $baohanh, $image_path);
        }
    }
    echo "<script>alert('Chỉ áp dụng định dạng ảnh png, jpg, jpeg');</script>";
    return false;
    }


}

$sanphamController = new SanPhamController();
$data = $sanphamController->getDanhSachSanPham();
$ds_tren_trang = $data['ds_sp'];
$page = $data['page'];
$total_pages = $data['total_pages'];

// Kiểm tra nếu có yêu cầu xóa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_masp'])) {
    $masp_xoa = $_POST['delete_masp'];
    $sanphamController = new SanPhamController();
    $sanphamController->xoaSanPham($masp_xoa);
    
}
// them sp
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $result = $sanphamController->themSanPham($_POST, $_FILES['image']);
    if ($result) {
        header("Location: index.php?message=Thêm sản phẩm thành công");
    } else {
        echo "Có lỗi xảy ra khi thêm sản phẩm.";
    }
}


