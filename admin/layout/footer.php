</div> 

<div style="height: 50px;"></div>

<footer class="bg-white border-top py-4 mt-auto">
    <div class="container">
        <div class="row align-items-center">
            
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <span class="text-muted">
                    &copy; <?= date('Y') ?> <strong>MalasBaca</strong>. 
                </span>
                <br>
                <small class="text-secondary">Dikelola oleh Admin MalasBaca.</small>
            </div>

            <div class="col-md-6 text-center text-md-end">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <a href="#" class="text-decoration-none text-muted small">Privacy Policy</a>
                    </li>
                    <li class="list-inline-item border-start ps-2">
                        <a href="#" class="text-decoration-none text-muted small">Terms of Service</a>
                    </li>
                    <li class="list-inline-item border-start ps-2">
                        <span class="text-muted small">v1.0.0</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    setTimeout(function() {
        let alertNode = document.querySelector('.alert');
        if (alertNode) {
            let alert = new bootstrap.Alert(alertNode);
            alert.close();
        }
    }, 3000);
</script>

</body>
</html>