if (document.querySelectorAll('.option input[type="radio"]').length > 0) {
    document.querySelectorAll('.option input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
            this.closest('.option').classList.add('selected');
        });
    });
}

if (document.getElementById('bankTransferBtn')) {
    document.getElementById('bankTransferBtn').addEventListener('click', function() {
        document.getElementById('bankForm').classList.add('show-form');
    });
}

if (document.getElementById('cancelBtn')) {
    document.getElementById('cancelBtn').addEventListener('click', function() {
        document.getElementById('bankForm').classList.remove('show-form');
    });
}