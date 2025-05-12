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
        $newFileName = $data['masp'] . '.' . $imageFileType;
        $target_file = $target_dir . $newFileName;

        $masp = $data['masp'];
        $tensp = $data['tensp'];
        $nsx = $data['nsx'];
        $phanloai = $data['phanloai'];
        $soluong = $data['soluong'];
        $giatien = $data['giatien'];
        $mota = $data['mota'];
        $baohanh = $data['baohanh'];
        $image_path = $target_file;

        // ✅ Kiểm tra trùng mã hoặc tên
        if ($this->model->isProductExist($masp, $tensp)) {
            echo "<script>alert('Mã sản phẩm hoặc tên sản phẩm đã tồn tại!');</script>";
            return false;
        }

        // ✅ Kiểm tra định dạng ảnh
        if (in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                echo "<script>alert('Thêm sản phẩm thành công!');</script>";
                return $this->model->addProduct($masp, $tensp, $nsx, $phanloai, $soluong, $giatien, $mota, $baohanh, $image_path);
            }
        }

        echo "<script>alert('Chỉ áp dụng định dạng ảnh png, jpg, jpeg');</script>";
        return false;
    }

    public function layThongTinSanPham($masp) {
        return $this->model->getProductById($masp);
    }

    public function suaSanPham($data, $image = null) {
        $masp = $data['masp'];
        $tensp = $data['tensp'];
        $nsx = $data['nsx'];
        $phanloai = $data['phanloai'];
        $soluong = $data['soluong'];
        $giatien = $data['giatien'];
        $mota = $data['mota'];
        $baohanh = $data['baohanh'];
        
        $image_path = null;
        if ($image && $image['error'] == 0) {
            $target_dir = "../media/image/Product_img/";
            $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
            
            if (in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
                $newFileName = $masp . '.' . $imageFileType;
                $target_file = $target_dir . $newFileName;
                
                if (move_uploaded_file($image["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                } else {
                    echo "<script>alert('Có lỗi khi upload ảnh!');</script>";
                    return false;
                }
            } else {
                echo "<script>alert('Chỉ áp dụng định dạng ảnh png, jpg, jpeg');</script>";
                return false;
            }
        }
        
        return $this->model->updateProduct($masp, $tensp, $nsx, $phanloai, $soluong, $giatien, $mota, $baohanh, $image_path);
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
    $currentPage = $_GET['page'] ?? 1;

    header("Location: ?page=" . $currentPage);
    
}
// them sp
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $result = $sanphamController->themSanPham($_POST, $_FILES['image']);
    if ($result) {
        echo "<script>alert('Thêm sản phẩm thành công!);</script>";
    } else {
        echo "<script>alert('Có lỗi khi tạo sản phẩm!');</script>";
    }

}
// sua
// Xử lý khi click nút sửa
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_masp'])) {
    $masp_edit = $_GET['edit_masp'];
    $sanpham_can_sua = $sanphamController->layThongTinSanPham($masp_edit);
}

// Xử lý khi submit form sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $result = $sanphamController->suaSanPham($_POST, $_FILES['image'] ?? null);
    if ($result) {
        echo "<script>alert('Thay đổi sản phẩm thành công!'); window.location.href = '?';</script>";
    } else {
        echo "<script>alert('Có lỗi khi cập nhật sản phẩm!');</script>";
    }
}


