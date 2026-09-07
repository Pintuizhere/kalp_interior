<!-- Estimate Section -->
<section class="estimate-section" style="padding: 100px 0; background-color: var(--bg-light);">
    <div class="container" style="max-width: 1200px;">
        <div class="estimate-header" style="text-align: center; margin-bottom: 60px;">
            <h2 class="section-title" style="font-size: 3rem; margin-bottom: 15px;">Get an estimate for your <span style="font-family: var(--font-accent); color: var(--accent-color); font-weight: 400; text-transform: lowercase;">home</span></h2>
            <p style="color: var(--text-muted); font-size: 16px; max-width: 600px; margin: 0 auto;">
                Select your property type to calculate the cost of your interiors.
            </p>
        </div>
        
        <div class="estimate-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
            <?php
            $estimate_options = [
                [
                    'icon' => 'house-line-thin',
                    'title' => 'Residential',
                    'desc' => 'Perfect for homes, apartments, and villas. Get a tailored estimate for your cozy space.'
                ],
                [
                    'icon' => 'buildings-thin',
                    'title' => 'Commercial',
                    'desc' => 'Ideal for offices, retail stores, and commercial layouts needing professional design.'
                ],
                [
                    'icon' => 'cooking-pot-thin',
                    'title' => 'Modular Kitchen',
                    'desc' => 'Modern, smart, and space-efficient kitchens designed to fit your lifestyle.'
                ]
            ];

            foreach ($estimate_options as $option) {
                echo '<div class="estimate-card" style="background: var(--bg-white); border-radius: 12px; padding: 40px 30px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.3s ease, box-shadow 0.3s ease;">';
                
                // Icon circle
                echo '<div style="width: 70px; height: 70px; border-radius: 50%; background-color: rgba(234, 177, 54, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 25px auto;">';
                echo '<img src="https://api.iconify.design/ph/' . $option['icon'] . '.svg?color=%23EAB136" alt="' . $option['title'] . '" style="width: 35px; height: 35px;">';
                echo '</div>';
                
                // Title
                echo '<h3 style="font-size: 24px; font-weight: 700; color: var(--text-dark); margin-bottom: 15px;">' . $option['title'] . '</h3>';
                
                // Description
                echo '<p style="color: var(--text-muted); font-size: 14px; line-height: 1.6; margin-bottom: 30px; min-height: 65px;">' . $option['desc'] . '</p>';
                
                // Button
                echo '<a href="calculator.php" class="estimate-btn" style="width: 100%; padding: 8px 15px 8px 8px; background: var(--text-dark); border: none; color: white; font-weight: 500; font-size: 15px; border-radius: 50px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 15px; text-decoration: none; box-sizing: border-box;">';
                echo '<span style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background-color: var(--accent-color); flex-shrink: 0; transition: transform 0.3s ease;" class="btn-icon-circle">';
                echo '<img src="https://api.iconify.design/ph/arrow-up-right-bold.svg?color=%231E2723" alt="arrow" style="width: 18px; height: 18px;">';
                echo '</span>';
                echo '<span style="flex-grow: 1; text-align: center; padding-right: 15px;">Calculate Estimate</span>';
                echo '</a>';
                
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<style>
.estimate-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.08) !important;
}
.estimate-btn:hover {
    background-color: var(--primary-color) !important;
}
.estimate-btn:hover .btn-icon-circle {
    transform: rotate(45deg);
}
@media (max-width: 1024px) {
    .estimate-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 768px) {
    .estimate-section { padding: 60px 15px !important; }
    .estimate-header { margin-bottom: 30px !important; }
    .estimate-header .section-title { font-size: 2.2rem !important; margin-bottom: 10px !important; }
    .estimate-header p { font-size: 15px !important; }
    .estimate-grid { gap: 20px !important; }
}
@media (max-width: 576px) {
    .estimate-grid { grid-template-columns: 1fr !important; }
    .estimate-card { padding: 30px 20px !important; }
    .estimate-card h3 { font-size: 20px !important; margin-bottom: 10px !important; }
    .estimate-card p { min-height: auto !important; margin-bottom: 25px !important; font-size: 13.5px !important; }
}
</style>
