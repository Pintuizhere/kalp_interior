    </div> <!-- End .main-content (started in index.php) -->
    </div> <!-- End .main-wrapper (started in index.php) -->
    </div> <!-- End .admin-layout (started in header.php) -->
    
    <!-- Custom Delete Modal -->
    <style>
    /* Inline CSS to bypass browser cache issues */
    .custom-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); z-index: 99999;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; visibility: hidden; transition: all 0.3s ease;
        backdrop-filter: blur(3px);
    }
    .custom-modal-overlay.active { opacity: 1; visibility: visible; }
    .custom-modal-box {
        background: #fff; width: 100%; max-width: 400px;
        border-radius: 16px; padding: 30px; text-align: center;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        transform: translateY(20px) scale(0.95); transition: all 0.3s ease;
    }
    .custom-modal-overlay.active .custom-modal-box { transform: translateY(0) scale(1); }
    .custom-modal-icon {
        width: 70px; height: 70px; background: #fee2e2; color: #ef4444;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 30px; margin: 0 auto 20px;
    }
    .custom-modal-title { font-size: 20px; font-weight: 700; color: #1E2723; margin-bottom: 10px; font-family: 'League Spartan', sans-serif;}
    .custom-modal-text { font-size: 14px; color: #66756C; margin-bottom: 25px; font-family: 'Inter', sans-serif;}
    .custom-modal-actions { display: flex; gap: 15px; justify-content: center; }
    .custom-modal-btn {
        padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px;
        cursor: pointer; border: none; transition: all 0.2s ease; font-family: 'Inter', sans-serif;
    }
    .custom-modal-btn.cancel { background: #f1f5f9; color: #64748b; }
    .custom-modal-btn.cancel:hover { background: #e2e8f0; }
    .custom-modal-btn.delete { background: #ef4444; color: #fff; text-decoration: none; }
    .custom-modal-btn.delete:hover { background: #dc2626; }
    </style>

    <div class="custom-modal-overlay" id="customDeleteModal">
        <div class="custom-modal-box">
            <div class="custom-modal-icon">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <h3 class="custom-modal-title">Are you sure?</h3>
            <p class="custom-modal-text">You won't be able to revert this! This action cannot be undone.</p>
            <div class="custom-modal-actions">
                <button type="button" class="custom-modal-btn cancel" onclick="closeCustomDeleteModal()">Cancel</button>
                <a href="#" id="customDeleteConfirmBtn" class="custom-modal-btn delete">Yes, delete it!</a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/admin.js"></script>
    <script>
    // Global Delete Confirmation Logic
    function closeCustomDeleteModal() {
        document.getElementById('customDeleteModal').classList.remove('active');
    }

    // Use event delegation to catch all clicks before native handlers run
    document.addEventListener('click', function(e) {
        // Allow the actual confirm button to work normally
        if (e.target.closest('#customDeleteConfirmBtn')) {
            return;
        }

        // Find if they clicked a delete button or an icon inside it
        const deleteLink = e.target.closest('a.delete') || e.target.closest('a[onclick*="confirm"]');
        
        if (deleteLink) {
            e.preventDefault();
            e.stopImmediatePropagation(); // Prevent native onclick confirm from running!
            
            // Just to be safe, remove the attribute
            if(deleteLink.hasAttribute('onclick')) {
                deleteLink.removeAttribute('onclick');
            }
            
            const deleteUrl = deleteLink.getAttribute('href');
            if (deleteUrl && deleteUrl !== '#' && !deleteUrl.startsWith('javascript')) {
                document.getElementById('customDeleteConfirmBtn').href = deleteUrl;
                document.getElementById('customDeleteModal').classList.add('active');
            }
        }
    }, true); // Use capture phase to intercept before inline onclick

    // Close modal when clicking outside the box
    document.getElementById('customDeleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCustomDeleteModal();
        }
    });
    </script>
</body>
</html>
