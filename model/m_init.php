<?php 
    require_once("m_database.php");
    
    class M_init extends M_database {   
        public function Create_Structure() {
            $sql_account = "Create Table If Not Exists Account (
                LevelID int(1) Not Null Default 0,
                MaTK int(6) Zerofill Unsigned Auto_Increment Primary Key ,
                TenTK varchar(100) Not Null,
                Password varchar(10) Not Null,
                Email varchar(100) Unique Not Null,
                SDT varchar(10) Unique Not Null,
                DiaChi varchar(100) Not Null
            )";
            $this->setQuery($sql_account);
            $this->excuteQuery();

            $sql_product = "Create Table If Not Exists Products (
                MaSP varchar(6) Primary Key,
                TenSP varchar(50) Unique Not Null,
                NSX varchar(15) Not Null,
                PhanLoai varchar(100) Not Null,
                SoLuong int Not Null,
                GiaTien float Not Null,
                MoTa varchar(100) Not Null,
                BaoHanh varchar(100) Not Null,
                ImageSP varchar(100) Not Null,
                TagName varchar(100) Not Null,
                Sold int Not Null Default 0,
                MaTK int(6) Zerofill,
                Constraint P_MaTK_FK Foreign Key (MaTK) References Account(MaTK) On Delete Cascade
            )";
            $this->setQuery($sql_product);
            $this->excuteQuery();

            $sql_Carts = "Create Table If Not Exists Cart (
                MaTK int(6) Zerofill Not Null,
                MaSP varchar(6) Not Null,
                SoLuong int Not Null,
                GiaTien float Not Null,
                State varchar(50) Not Null,
                NgayMua TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                Primary Key (MaTK, MaSP),
                Constraint C_MaTK_FK Foreign Key (MaTK) References Account(MaTK) On Delete cascade,
                Constraint C_MaSP_FK Foreign Key (MaSP) References Products(MaSP) On Delete cascade
                )";
            $this->setQuery($sql_Carts);
            $this->excuteQuery();

             $sql_HD = "Create Table If Not Exists HoaDon (
                MaHD int(6) Zerofill Unsigned Auto_Increment Primary Key,
                MaTK int(6) Zerofill Not Null,
                SoTien float Not Null,
                NgayThanhToan TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                Constraint HD_MaTK_FK Foreign Key (MaTK) References Account(MaTK) On Delete cascade
                )";
            $this->setQuery($sql_HD);
            $this->excuteQuery();

            $sql_LS_Mua = "Create Table If Not Exists LS_Mua (
                MaHD int(6) Zerofill Not Null,
                MaTK int(6) Zerofill Not Null,
                MaSP varchar(6) Not Null,
                TenSP varchar(50) Not Null,            
                SoLuong int Not Null,
                GiaTien float Not Null,
                State varchar(50) Not Null,
                NgayMua TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                primary key (MaHD, MaSP),
                Constraint LS_MaHD_FK Foreign Key (MaHD) References HoaDon(MaHD) On Delete cascade,
                Constraint LS_MaSP_FK Foreign Key (MaSP) References Products(MaSP) On Delete cascade
            )";
            $this->setQuery($sql_LS_Mua);
            $this->excuteQuery();

            $sql_Voucher = "Create Table If Not Exists Vouchers (
                MaV varchar(10) Not Null Primary Key,
                Discount float Not Null
                )";
            $this->setQuery($sql_Voucher);
            $this->excuteQuery();
        }
        public function Insert_Data() {
            $sql_ad_1 = "INSERT INTO Account (LevelID, TenTK, Email, SDT, DiaChi) VALUES
                        (1 , 'Trương Anh Kiệt', 'anhkiet@gmail.com', '0987654321', 'TP Hồ Chí Minh')";
            $this->setQuery($sql_ad_1);
            $this->excuteQuery();

            // Đọc file products.json
            $jsonData = file_get_contents('../public/Data/products.json');
            $products = json_decode($jsonData, true);
            
            foreach ($products as $product) {
                $MaSP = $product['MaSP'];
                $TenSP = addslashes($product['TenSP']);
                $NSX = $product['NSX'];
                $PhanLoai = addslashes($product['PhanLoai']);
                $SoLuong = (int)$product['SoLuong'];
                $GiaTien = (float)$product['GiaTien'];
                $MoTa = addslashes($product['MoTa']);
                $BaoHanh = addslashes($product['BaoHanh']);
                $ImageSP = addslashes($product['ImageSP']);
                $MaTK = $product['MaTK'];

                $sql_pro = "INSERT INTO Products (MaSP, TenSP, NSX, PhanLoai, SoLuong, GiaTien, Mota, BaoHanh, ImageSP, MaTK)
                            VALUES ('$MaSP', '$TenSP', '$NSX', '$PhanLoai', $SoLuong, $GiaTien, '$MoTa', '$BaoHanh', '$ImageSP', '$MaTK')";
                $this->setQuery($sql_pro);
                $this->excuteQuery();
            }

            $sql_vou_1 = "INSERT INTO Vouchers (MaV, Discount) VALUES
                            ('VCNamMoi', 50),
                            ('VCMungTet', 60),
                            ('VC30T4', 20)";
            $this->setQuery($sql_vou_1);
            $this->excuteQuery();
        }
    }
    $myInit = new M_init();
    $myInit->Create_Structure();
    $myInit->Insert_Data();

    echo "Cơ sở dữ liệu đã được tạo thành công!";
?>