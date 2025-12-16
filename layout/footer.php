<?php
// Basic footer
?>
<script>
// Auto-close modals after form submission
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-modal]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const modalId = form.getAttribute('data-modal');
            if (modalId) {
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    if (modal) modal.hide();
                }, 500);
            }
        });
    });
});
</script>
</body>
</html>
