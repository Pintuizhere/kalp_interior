        <div class="up-login-section">
            <button class="up-close-btn" onclick="closeUserPanel()"><i class="fa-solid fa-xmark"></i></button>
            
            <!-- Login Form -->
            <div class="up-login-content" id="loginFormSection">
                <h2>Welcome</h2>
                <p>Please login to view your orders and profile.</p>
                
                <?php if(isset($_SESSION['auth_error'])): ?>
                    <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                        <?php echo $_SESSION['auth_error']; unset($_SESSION['auth_error']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="up-form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="user@example.com" class="up-input" required>
                    </div>
                    <div class="up-form-group">
                        <label>Password</label>
                        <div style="position: relative;">
                            <input type="password" name="password" id="loginPassword" placeholder="••••••••" class="up-input" style="padding-right: 40px;" required>
                            <button type="button" onclick="togglePassword('loginPassword', 'loginEyeIcon')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #888; cursor: pointer;">
                                <i class="fa-regular fa-eye" id="loginEyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn-brand" style="width: 100%; justify-content: center; border-radius: 8px;">Login</button>
                </form>
                
                <div style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
                    Don't have an account? <a href="#" onclick="toggleAuthForm('register'); return false;" style="color: var(--brand-green); font-weight: 600; text-decoration: none;">Create account</a>
                </div>
            </div>

            <!-- Register Form (Hidden by default) -->
            <div class="up-login-content" id="registerFormSection" style="display: none;">
                <h2>Create Account</h2>
                <p>Sign up to manage your orders and profile.</p>
                
                <form method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="up-form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" placeholder="John Doe" class="up-input" required>
                    </div>
                    <div class="up-form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="user@example.com" class="up-input" required>
                    </div>
                    <div class="up-form-group">
                        <label>Password</label>
                        <div style="position: relative;">
                            <input type="password" name="password" id="registerPassword" placeholder="••••••••" class="up-input" style="padding-right: 40px;" required>
                            <button type="button" onclick="togglePassword('registerPassword', 'registerEyeIcon')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #888; cursor: pointer;">
                                <i class="fa-regular fa-eye" id="registerEyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn-brand" style="width: 100%; justify-content: center; border-radius: 8px;">Sign Up</button>
                </form>
                
                <div style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
                    Already have an account? <a href="#" onclick="toggleAuthForm('login'); return false;" style="color: var(--brand-green); font-weight: 600; text-decoration: none;">Login</a>
                </div>
            </div>
        </div>

        <script>
            function toggleAuthForm(type) {
                if (type === 'register') {
                    document.getElementById('loginFormSection').style.display = 'none';
                    document.getElementById('registerFormSection').style.display = 'block';
                } else {
                    document.getElementById('registerFormSection').style.display = 'none';
                    document.getElementById('loginFormSection').style.display = 'block';
                }
            }

            function togglePassword(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        </script>
