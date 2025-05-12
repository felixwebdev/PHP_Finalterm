<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebarMenu');
        const mainContent = document.getElementById('mainContent');

        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            mainContent.style.marginLeft = sidebar.classList.contains('collapsed') ? '70px' : '250px';
        });

        // ==== Gán class "active" dựa trên URL tuyệt đối ====
        const currentURL = window.location.href;
        const navLinks = document.querySelectorAll('.sidebar a');

        navLinks.forEach(link => {
            const linkURL = new URL(link.href, window.location.origin).href;

            if (currentURL === linkURL) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
</script>
<script>
    window.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has("page")) {
            // Cuộn đến vị trí bảng sản phẩm (hoặc ID nào bạn muốn)
            const table = document.querySelector("table");
            if (table) {
                table.scrollIntoView({ behavior: "smooth" });
            }
        }
    });
</script>
<script>
    document.getElementById('logoutButton').addEventListener('click', function (e) {
        e.preventDefault(); // Ngăn chuyển hướng mặc định

        // Nếu bạn cần xác nhận:
        if (confirm("Bạn có chắc chắn muốn đăng xuất không?")) {
            window.location.href = "../signIn.php";
        }
    });
</script>


