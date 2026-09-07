<!-- End-to-End Solutions Section -->
<section class="e2e-section" style="padding: 100px 0; background-color: var(--bg-white);">
    <div class="container" style="max-width: 1200px;">
        <div class="e2e-header" style="text-align: center; margin-bottom: 60px;">
            <h2 class="section-title" style="font-size: 3rem; margin-bottom: 20px;">End-to-end <span style="font-family: var(--font-accent); color: var(--accent-color); font-weight: 400; font-style: normal; text-transform: lowercase;">interior solutions</span></h2>
            
            <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 20px;">
                <div style="width: 40px; height: 1px; background-color: var(--accent-color);"></div>
                <div style="width: 5px; height: 5px; border-radius: 50%; background-color: var(--accent-color);"></div>
                <div style="width: 40px; height: 1px; background-color: var(--accent-color);"></div>
            </div>
            
            <p style="color: var(--text-muted); font-size: 16px; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                Complete interior solutions tailored to your style, needs, and lifestyle.<br>
                From design to delivery — we've got you covered.
            </p>
        </div>
        
        <div class="e2e-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 50px 20px;">
            <?php
            $services = [
                ['icon' => 'oven-thin', 'title' => 'Modular Kitchen'],
                ['icon' => 'door-thin', 'title' => 'Storage and wardrobe'],
                ['icon' => 'coffee-thin', 'title' => 'Crockery Units'],
                ['icon' => 'armchair-thin', 'title' => 'Space Saving Furniture'],
                ['icon' => 'television-thin', 'title' => 'TV Units'],
                ['icon' => 'desktop-thin', 'title' => 'Study Tables'],
                ['icon' => 'squares-four-thin', 'title' => 'False Ceiling'],
                ['icon' => 'lamp-thin', 'title' => 'Lights'],
                ['icon' => 'image-thin', 'title' => 'Wallpaper'],
                ['icon' => 'paint-roller-thin', 'title' => 'Wall Paint'],
                ['icon' => 'drop-thin', 'title' => 'Bathroom'],
                ['icon' => 'hands-praying-thin', 'title' => 'Pooja Unit'],
                ['icon' => 'door-open-thin', 'title' => 'Foyer Designs'],
                ['icon' => 'chair-thin', 'title' => 'Movable furniture'],
                ['icon' => 'bed-thin', 'title' => 'Kids Bedroom']
            ];

            foreach ($services as $service) {
                echo '<div class="e2e-item" style="text-align: center; cursor: pointer; transition: transform 0.3s ease;">';
                echo '<div class="e2e-icon-wrapper" style="position: relative; display: inline-block; margin-bottom: 15px;">';
                // Using iconify api for SVG outline icons (color matches text-dark #1E2723)
                echo '<img src="https://api.iconify.design/ph/' . $service['icon'] . '.svg?color=%231E2723" alt="' . $service['title'] . '" class="e2e-img" style="width: 50px; height: 50px; transition: 0.3s ease;">';
                // Small accent line for the dual-tone effect using theme accent color
                echo '<div style="position: absolute; bottom: 8px; right: -5px; width: 12px; height: 2px; background-color: var(--accent-color);"></div>';
                echo '<div style="position: absolute; top: 8px; left: -2px; width: 4px; height: 4px; border-radius: 50%; background-color: var(--accent-color);"></div>';
                echo '</div>';
                echo '<h5 style="font-size: 13px; font-weight: 500; color: var(--text-dark); margin: 0; line-height: 1.4; text-transform: uppercase; letter-spacing: 1px;">' . $service['title'] . '</h5>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<style>
@media (max-width: 992px) {
    .e2e-grid { grid-template-columns: repeat(3, 1fr) !important; gap: 40px 15px !important; }
}
@media (max-width: 768px) {
    .e2e-section { padding: 60px 15px !important; }
    .e2e-header { margin-bottom: 40px !important; }
    .e2e-header .section-title { font-size: 2.2rem !important; margin-bottom: 15px !important; }
    .e2e-header p { font-size: 14.5px !important; padding: 0 10px !important; }
    .e2e-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 30px 10px !important; }
    .e2e-item h5 { font-size: 11.5px !important; }
    .e2e-img { width: 40px !important; height: 40px !important; }
}
.e2e-item:hover {
    transform: translateY(-5px);
}
.e2e-item:hover .e2e-img {
    opacity: 0.8;
}
</style>
