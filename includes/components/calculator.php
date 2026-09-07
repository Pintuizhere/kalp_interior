<?php
require_once 'admin/config/db.php';
$calc_cats = $conn->query("SELECT * FROM calc_categories ORDER BY id ASC");
$calc_cats_data = [];
while($row = $calc_cats->fetch_assoc()){ 
    if (isset($exclude_category) && $row['slug'] === $exclude_category) continue;
    $calc_cats_data[] = $row; 
}

$calc_types = $conn->query("SELECT * FROM calc_types ORDER BY category_slug ASC, id ASC");
$calc_types_data = [];
while($row = $calc_types->fetch_assoc()){ $calc_types_data[$row['category_slug']][] = $row; }

$calc_styles = $conn->query("SELECT * FROM calc_styles ORDER BY position ASC, percent_value ASC");
$calc_styles_data = [];
while($row = $calc_styles->fetch_assoc()){ $calc_styles_data[] = $row; }

$calc_packages = $conn->query("SELECT * FROM calc_packages ORDER BY category_slug ASC, price_per_sqft ASC");
$calc_packages_data = [];
while($row = $calc_packages->fetch_assoc()){ $calc_packages_data[$row['category_slug']][] = $row; }

$calc_addons = $conn->query("SELECT * FROM calc_addons ORDER BY id ASC");
$calc_addons_data = [];
while($row = $calc_addons->fetch_assoc()){ $calc_addons_data[] = $row; }

$calc_settings = [];
$res = $conn->query("SELECT setting_key, setting_value FROM calculator_settings");
if($res){ while($row = $res->fetch_assoc()){ $calc_settings[$row['setting_key']] = $row['setting_value']; } }

$calc_breakdowns = $conn->query("SELECT * FROM calc_breakdowns ORDER BY category_slug ASC, position ASC");
$calc_breakdowns_data = [];
while($row = $calc_breakdowns->fetch_assoc()){ $calc_breakdowns_data[$row['category_slug']][] = $row; }
?>
<style>
    @media (max-width: 768px) {
        .calculator-section { padding: 60px 15px !important; }
        .calc-title { font-size: 2rem !important; line-height: 1.3 !important; }
        .calc-form-col, .calc-results-col { padding: 25px 15px !important; border-radius: 15px !important; }
        
        /* Make form elements stack gracefully */
        .calc-options-grid { grid-template-columns: 1fr !important; gap: 10px !important; }
        
        /* Exception: keep 2 columns for primary choices if they fit, or let them stack on very small screens */
        .main-category-options { grid-template-columns: repeat(2, 1fr) !important; }
        
        .calc-header { margin-bottom: 30px; text-align: center; }
        .calc-title-box p { font-size: 1rem !important; }
        .calc-header-features { gap: 15px; }
        
        /* Result column tweaks */
        .results-amount h2 { font-size: 36px !important; }
        .breakdown-row { font-size: 14px !important; }
        .results-cta { padding: 15px !important; flex-direction: column; text-align: center; }
        .results-cta .cta-arrow { align-self: center; margin-top: 10px; }
        
        .cf-badge { width: 100% !important; margin-bottom: 15px; display: flex; align-items: flex-start; }
        .calc-footer-badges { padding: 25px !important; flex-direction: column; background: #fff; border-radius: 15px; }
    }
    
    @media (max-width: 480px) {
        .calculator-section { padding: 40px 10px !important; }
        .calc-title { font-size: 1.6rem !important; }
        .calc-form-col, .calc-results-col { padding: 20px 15px !important; }
        
        /* Force 1 column on tiny screens to avoid cramped text */
        .main-category-options, .type-options, .style-options, .finish-options { 
            grid-template-columns: 1fr !important; 
        }
        
        .results-amount h2 { font-size: 28px !important; }
        .results-cta .cta-text strong { font-size: 14px !important; }
        .wi-text p { font-size: 12px !important; }
    }
    
    .k-package-card .k-pkg-radio {
        transition: 0.2s all;
        position: relative;
    }
    .k-package-card.active .k-pkg-radio {
        border-color: white !important;
    }
    .k-package-card.active .k-pkg-radio::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 8px;
        height: 8px;
        background-color: white;
        border-radius: 50%;
    }
</style>
<section class="calculator-section" style="padding: 100px 0; background-color: var(--primary-color);">
    <div class="container" style="max-width: 1400px;">
        
        <!-- Calculator Header -->
        <div class="calc-header">
            <div class="calc-title-box">
                <h1 class="calc-title" style="color: white; font-size: 2.5rem; margin-bottom: 10px;">Interior Design Estimate Calculator</h1>
                <p style="color: rgba(255,255,255,0.7); font-size: 1.1rem;">Get an instant estimate for your dream space</p>
            </div>
            
            <div class="calc-header-features">
                <div class="calc-h-feature">
                    <div class="calc-h-icon"><i class="fa-solid fa-bolt"></i></div>
                    <div class="calc-h-text">
                        <strong>Instant Estimate</strong>
                        <span>Get results in seconds</span>
                    </div>
                </div>
                <div class="calc-h-feature">
                    <div class="calc-h-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <div class="calc-h-text">
                        <strong>Accurate Pricing</strong>
                        <span>Real-time market rates</span>
                    </div>
                </div>
                <div class="calc-h-feature">
                    <div class="calc-h-icon"><i class="fa-solid fa-magnifying-glass-dollar"></i></div>
                    <div class="calc-h-text">
                        <strong>Transparent</strong>
                        <span>No hidden charges</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calculator Body -->
        <div class="calc-body">
            
            <!-- Left Column: Form -->
            <div class="calc-form-col">
                <div class="calc-form-header">
                    <h3>Configure Your Project</h3>
                    <p>Fill in the details below to get your estimated cost range</p>
                </div>
                
                <form id="estimate-calculator-form">
                    
                    <div class="calc-row" <?php echo (isset($force_category) && $force_category) ? 'style="display:none;"' : ''; ?>>
                        <!-- Step 1: Property Type -->
                        <div class="calc-step-group" style="flex: 2;">
                            <div class="calc-step-label">
                                <span class="step-num">1</span>
                                <label>Select Property Category</label>
                            </div>
                            <div class="calc-options-grid main-category-options" style="grid-template-columns: repeat(<?php echo count($calc_cats_data); ?>, 1fr); margin-bottom: 20px;">
                                <?php $is_first = true; foreach($calc_cats_data as $cat): ?>
                                <label class="calc-option-card <?php echo $is_first ? 'active' : ''; ?>" data-target="<?php echo $cat['slug']; ?>-options" <?php echo ($cat['slug']=='kitchen' || $cat['slug']=='modular-kitchen') ? 'id="cat-kitchen"' : ''; ?>>
                                    <input type="radio" name="property_category" value="<?php echo $cat['slug']; ?>" <?php echo $is_first ? 'checked' : ''; ?>>
                                    <i class="<?php echo htmlspecialchars($cat['icon']); ?>" style="color: <?php echo $cat['slug']=='residential' ? '#F4B41A' : ($cat['slug']=='commercial' ? '#4B95C4' : '#E74C3C'); ?>;"></i>
                                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                </label>
                                <?php $is_first = false; endforeach; ?>
                            </div>

                            <div class="calc-step-label" id="specific-type-label">
                                <span class="step-num"><i class="fa-solid fa-arrow-right"></i></span>
                                <label>Select Specific Type</label>
                            </div>
                            
                            <?php foreach($calc_cats_data as $cat): 
                                $slug = $cat['slug'];
                                if($slug == 'kitchen' || $slug == 'modular-kitchen') continue; // Kitchen doesn't have specific types
                                $is_residential = ($slug == 'residential');
                            ?>
                            <div id="<?php echo $slug; ?>-options" class="calc-options-grid type-options sub-options-group" <?php echo !$is_residential ? 'style="display: none;"' : ''; ?>>
                                <?php if(isset($calc_types_data[$slug])): ?>
                                    <?php $is_first_type = true; foreach($calc_types_data[$slug] as $type): ?>
                                    <label class="calc-option-card <?php echo ($is_residential && $is_first_type) ? 'active' : ''; ?>">
                                        <input type="radio" name="property_type" value="<?php echo $type['sqft']; ?>" <?php echo ($is_residential && $is_first_type) ? 'checked' : ''; ?>>
                                        <i class="<?php echo htmlspecialchars($type['icon']); ?>" style="color: <?php echo $is_residential ? '#E67E22' : '#27AE60'; ?>;"></i>
                                        <span><?php echo htmlspecialchars($type['name']); ?></span>
                                        <?php if($type['sqft'] > 0): ?>
                                        <span style="font-size: 11px; opacity: 0.7;"><?php echo $type['sqft']; ?> sq ft</span>
                                        <?php endif; ?>
                                    </label>
                                    <?php $is_first_type = false; endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>

                    </div>

                    <!-- Standard Steps wrapper -->
                    <div id="standard-calc-steps" <?php echo (isset($force_category) && $force_category === 'modular-kitchen') ? 'style="display:none;"' : ''; ?>>

                    <!-- Step 2: Square Footage -->
                    <div class="calc-step-group" id="sqft-step-group" style="margin-bottom: 30px;">
                        <div class="calc-step-label">
                            <span class="step-num">2</span>
                            <label>Enter Square Foot</label>
                        </div>
                        <div class="calc-input-wrapper">
                            <input type="number" name="sqft" id="sqft-input" class="calc-input" placeholder="Enter Exact Area (sqft)" value="700" style="width: 100%; padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: white; font-family: inherit; font-size: 16px; outline: none; box-sizing: border-box;" min="1">
                        </div>
                        <div id="sqft-error" style="color: #E74C3C; font-size: 13px; margin-top: 8px; display: none; font-weight: 500;">
                            <i class="fa-solid fa-circle-exclamation"></i> Square footage must be at least 400 sqft.
                        </div>
                    </div>

                    <!-- Step 3: Design Style -->
                    <div class="calc-step-group">
                        <div class="calc-step-label">
                            <span class="step-num">3</span>
                            <label>Select Design Style</label>
                        </div>
                        <div class="calc-options-grid style-options">
                            <?php 
                            $is_first_res = true;
                            foreach($calc_styles_data as $style): 
                                $cat_slug = $style['category_slug'] ?? 'residential';
                                $is_active = ($cat_slug == 'residential' && $is_first_res);
                                if ($cat_slug == 'residential') $is_first_res = false;
                            ?>
                            <label class="calc-option-card style-card <?php echo $is_active ? 'active' : ''; ?>" data-category="<?php echo $cat_slug; ?>" style="<?php echo $cat_slug == 'commercial' ? 'display: none;' : ''; ?> flex-direction: column; text-align: center; padding: 15px;">
                                <input type="radio" name="design_style" value="<?php echo $style['percent_value']; ?>" <?php echo $is_active ? 'checked' : ''; ?>>
                                <i class="<?php echo htmlspecialchars($style['icon']); ?>" style="color: <?php 
                                    $colors = ['#2ECC71', '#A9CCE3', '#F1C40F', '#3498DB', '#9B59B6', '#E67E22', '#1ABC9C'];
                                    echo $colors[array_rand($colors)]; 
                                ?>;"></i>
                                <span><?php echo htmlspecialchars($style['name']); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="calc-row">
                        <!-- Step 4: Finishing Level -->
                        <div class="calc-step-group" style="flex: 1.5;">
                            <div class="calc-step-label">
                                <span class="step-num">4</span>
                                <label>Select Package</label>
                            </div>
                            <div class="calc-options-grid finish-options">
                                <?php 
                                foreach(['residential', 'commercial'] as $cat_slug):
                                    if(isset($calc_packages_data[$cat_slug])): 
                                        $is_first_pkg = true; 
                                        foreach($calc_packages_data[$cat_slug] as $pkg): ?>
                                <label class="calc-option-card pkg-card <?php echo ($cat_slug=='residential' && $is_first_pkg) ? 'active' : ''; ?>" data-category="<?php echo $cat_slug; ?>" style="flex-direction: row; text-align: left; align-items: center; justify-content: flex-start; padding: 15px; <?php echo $cat_slug == 'commercial' ? 'display:none;' : ''; ?>">
                                    <input type="radio" name="finish_level" value="<?php echo $pkg['price_per_sqft']; ?>" <?php echo ($cat_slug=='residential' && $is_first_pkg) ? 'checked' : ''; ?>>
                                    <?php echo $pkg['icon_svg']; ?>
                                    <div>
                                        <span style="display: block; font-weight: 600;"><?php echo htmlspecialchars($pkg['name']); ?></span>
                                        <span style="font-size: 11px; opacity: 0.7; display: none;">₹<?php echo $pkg['price_per_sqft']; ?>/sqft</span>
                                    </div>
                                </label>
                                <?php $is_first_pkg = false; endforeach; endif; endforeach; ?>
                            </div>
                        </div>

                        <!-- Step 5: Add-ons -->
                        <div class="calc-step-group" style="flex: 1;">
                            <div class="calc-step-label">
                                <span class="step-num">5</span>
                                <label>Add-ons (Optional)</label>
                            </div>
                            <div class="calc-checkbox-grid addons">
                                <?php 
                                foreach($calc_addons_data as $addon): 
                                    if ($addon['category_slug'] === 'modular-kitchen') continue;
                                    $cat_slug = $addon['category_slug'] ?? 'residential';
                                ?>
                                <label class="calc-checkbox addon-card" data-category="<?php echo $cat_slug; ?>" style="<?php echo $cat_slug == 'commercial' ? 'display:none;' : ''; ?>">
                                    <input type="checkbox" name="addons" value="<?php echo $addon['percent_value']; ?>">
                                    <span class="chk-box"><i class="fa-solid fa-check"></i></span> <?php echo htmlspecialchars($addon['name']); ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="calc-estimate-btn" class="calc-submit-btn">
                        <i class="fa-solid fa-calculator"></i> Calculate Estimate
                    </button>
                    
                    <p class="calc-disclaimer">
                        <i class="fa-solid fa-circle-info"></i> This is an estimated range. Final cost may vary based on design, material selection & site conditions.
                    </p>
                    
                    </div> <!-- End standard-calc-steps -->

                    <!-- Modular Kitchen Options -->
                    <div id="kitchen-options" class="sub-options-group" style="<?php echo (isset($force_category) && $force_category === 'modular-kitchen') ? 'display:block;' : 'display: none;'; ?> padding-top: 20px;">
                    
                    <!-- Progress Bar for Kitchen -->
                    <div class="kitchen-progress-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; position: relative;">
                        <div style="position: absolute; top: 15px; left: 10%; right: 10%; height: 2px; background: rgba(255,255,255,0.2); z-index: 1;">
                            <div id="kitchen-progress-fill" style="height: 100%; width: 0%; background: #2ECC71; transition: width 0.3s ease;"></div>
                        </div>
                        
                        <div class="k-step-indicator active" data-step="1" style="position: relative; z-index: 2; text-align: center; width: 20%;">
                            <div class="k-step-circle" style="width: 32px; height: 32px; border-radius: 50%; background: #2ECC71; color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 2px solid #2ECC71;">1</div>
                            <span style="font-size: 12px; color: white;">Kitchen Layout</span>
                        </div>
                        <div class="k-step-indicator" data-step="2" style="position: relative; z-index: 2; text-align: center; width: 20%;">
                            <div class="k-step-circle" style="width: 32px; height: 32px; border-radius: 50%; background: #334C40; color: rgba(255,255,255,0.5); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 2px solid rgba(255,255,255,0.2);">2</div>
                            <span style="font-size: 12px; color: rgba(255,255,255,0.5);">Measurements</span>
                        </div>
                        <div class="k-step-indicator" data-step="3" style="position: relative; z-index: 2; text-align: center; width: 20%;">
                            <div class="k-step-circle" style="width: 32px; height: 32px; border-radius: 50%; background: #334C40; color: rgba(255,255,255,0.5); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 2px solid rgba(255,255,255,0.2);">3</div>
                            <span style="font-size: 12px; color: rgba(255,255,255,0.5);">Package</span>
                        </div>
                        <div class="k-step-indicator" data-step="4" style="position: relative; z-index: 2; text-align: center; width: 20%;">
                            <div class="k-step-circle" style="width: 32px; height: 32px; border-radius: 50%; background: #334C40; color: rgba(255,255,255,0.5); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 2px solid rgba(255,255,255,0.2);">4</div>
                            <span style="font-size: 12px; color: rgba(255,255,255,0.5);">Accessories</span>
                        </div>
                        <div class="k-step-indicator" data-step="5" style="position: relative; z-index: 2; text-align: center; width: 20%;">
                            <div class="k-step-circle" style="width: 32px; height: 32px; border-radius: 50%; background: #334C40; color: rgba(255,255,255,0.5); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 2px solid rgba(255,255,255,0.2);">5</div>
                            <span style="font-size: 12px; color: rgba(255,255,255,0.5);">Get Quote</span>
                        </div>
                    </div>

                    <!-- Kitchen Step 1: Layout -->
                    <div id="kitchen-step-1" class="kitchen-step-content active" style="display: block; margin-bottom: 40px;">
                        <h3 style="text-align: center; color: white; margin-bottom: 25px;">Select the layout of your kitchen</h3>
                        <div class="calc-options-grid" style="grid-template-columns: repeat(3, 1fr); gap: 15px;">
                            
                            <label class="calc-option-card kitchen-layout-card">
                                <input type="radio" name="k_layout" value="straight">
                                <div style="width: 100%; height: 120px; background: #FFFFFF; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; position: relative; overflow: hidden;">
                                    <svg width="100%" height="100%" viewBox="0 0 160 120">
                                        <path d="M 10 90 L 10 10 L 150 10 L 150 90" stroke="#BDBDBD" stroke-width="4" fill="none" stroke-linecap="square"/>
                                        <rect x="12" y="12" width="136" height="25" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <g transform="translate(20, 14)"><rect width="20" height="20" fill="#fff" stroke="#000" stroke-width="1"/><circle cx="6" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="6" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/></g>
                                        <g transform="translate(68, 14)"><rect width="24" height="20" rx="2" fill="#fff" stroke="#000" stroke-width="1"/><rect x="2" y="2" width="20" height="12" rx="1" fill="none" stroke="#000" stroke-width="1"/><path d="M 12 14 L 12 18" stroke="#000" stroke-width="1"/></g>
                                        <text x="130" y="27" font-size="20" fill="#000" font-family="sans-serif" text-anchor="middle" dominant-baseline="central">*</text>
                                    </svg>
                                </div>
                                <span>Single Wall</span>
                            </label>

                            <label class="calc-option-card kitchen-layout-card">
                                <input type="radio" name="k_layout" value="parallel">
                                <div style="width: 100%; height: 120px; background: #FFFFFF; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; position: relative; overflow: hidden;">
                                    <svg width="100%" height="100%" viewBox="0 0 160 120">
                                        <path d="M 10 40 L 10 10 L 150 10 L 150 40 M 10 80 L 10 110 L 150 110 L 150 80" stroke="#BDBDBD" stroke-width="4" fill="none" stroke-linecap="square"/>
                                        <rect x="12" y="12" width="136" height="25" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <rect x="12" y="83" width="136" height="25" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <g transform="translate(20, 14)"><rect width="20" height="20" fill="#fff" stroke="#000" stroke-width="1"/><circle cx="6" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="6" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/></g>
                                        <g transform="translate(68, 14)"><rect width="24" height="20" rx="2" fill="#fff" stroke="#000" stroke-width="1"/><rect x="2" y="2" width="20" height="12" rx="1" fill="none" stroke="#000" stroke-width="1"/><path d="M 12 14 L 12 18" stroke="#000" stroke-width="1"/></g>
                                        <text x="130" y="98" font-size="20" fill="#000" font-family="sans-serif" text-anchor="middle" dominant-baseline="central">*</text>
                                    </svg>
                                </div>
                                <span>Double Galley</span>
                            </label>

                            <label class="calc-option-card active kitchen-layout-card">
                                <input type="radio" name="k_layout" value="l-shaped" checked>
                                <div style="width: 100%; height: 120px; background: #FFFFFF; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; position: relative; overflow: hidden;">
                                    <svg width="100%" height="100%" viewBox="0 0 160 120">
                                        <path d="M 10 110 L 10 10 L 150 10 L 150 110" stroke="#BDBDBD" stroke-width="4" fill="none" stroke-linecap="square"/>
                                        <rect x="12" y="12" width="136" height="25" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <rect x="123" y="37" width="25" height="71" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <g transform="translate(20, 14)"><rect width="20" height="20" fill="#fff" stroke="#000" stroke-width="1"/><circle cx="6" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="6" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/></g>
                                        <g transform="translate(68, 14)"><rect width="24" height="20" rx="2" fill="#fff" stroke="#000" stroke-width="1"/><rect x="2" y="2" width="20" height="12" rx="1" fill="none" stroke="#000" stroke-width="1"/><path d="M 12 14 L 12 18" stroke="#000" stroke-width="1"/></g>
                                        <text x="135" y="80" font-size="20" fill="#000" font-family="sans-serif" text-anchor="middle" dominant-baseline="central">*</text>
                                    </svg>
                                </div>
                                <span>L-Shape</span>
                            </label>
                            
                            <label class="calc-option-card kitchen-layout-card">
                                <input type="radio" name="k_layout" value="u-shaped">
                                <div style="width: 100%; height: 120px; background: #FFFFFF; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; position: relative; overflow: hidden;">
                                    <svg width="100%" height="100%" viewBox="0 0 160 120">
                                        <path d="M 10 110 L 10 10 L 150 10 L 150 110" stroke="#BDBDBD" stroke-width="4" fill="none" stroke-linecap="square"/>
                                        <rect x="12" y="12" width="136" height="25" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <rect x="12" y="37" width="25" height="71" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <rect x="123" y="37" width="25" height="71" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <g transform="translate(45, 14)"><rect width="20" height="20" fill="#fff" stroke="#000" stroke-width="1"/><circle cx="6" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="6" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/></g>
                                        <g transform="translate(90, 14)"><rect width="24" height="20" rx="2" fill="#fff" stroke="#000" stroke-width="1"/><rect x="2" y="2" width="20" height="12" rx="1" fill="none" stroke="#000" stroke-width="1"/><path d="M 12 14 L 12 18" stroke="#000" stroke-width="1"/></g>
                                        <text x="135" y="80" font-size="20" fill="#000" font-family="sans-serif" text-anchor="middle" dominant-baseline="central">*</text>
                                    </svg>
                                </div>
                                <span>U-Shape</span>
                            </label>

                            <label class="calc-option-card kitchen-layout-card">
                                <input type="radio" name="k_layout" value="peninsula">
                                <div style="width: 100%; height: 120px; background: #FFFFFF; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; position: relative; overflow: hidden;">
                                    <svg width="100%" height="100%" viewBox="0 0 160 120">
                                        <path d="M 10 110 L 10 10 L 150 10 L 150 110" stroke="#BDBDBD" stroke-width="4" fill="none" stroke-linecap="square"/>
                                        <rect x="12" y="12" width="136" height="25" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <rect x="123" y="37" width="25" height="71" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <rect x="12" y="83" width="70" height="25" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <g transform="translate(20, 14)"><rect width="20" height="20" fill="#fff" stroke="#000" stroke-width="1"/><circle cx="6" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="6" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/></g>
                                        <g transform="translate(68, 14)"><rect width="24" height="20" rx="2" fill="#fff" stroke="#000" stroke-width="1"/><rect x="2" y="2" width="20" height="12" rx="1" fill="none" stroke="#000" stroke-width="1"/><path d="M 12 14 L 12 18" stroke="#000" stroke-width="1"/></g>
                                        <text x="135" y="80" font-size="20" fill="#000" font-family="sans-serif" text-anchor="middle" dominant-baseline="central">*</text>
                                    </svg>
                                </div>
                                <span>Peninsula</span>
                            </label>

                            <label class="calc-option-card kitchen-layout-card">
                                <input type="radio" name="k_layout" value="island">
                                <div style="width: 100%; height: 120px; background: #FFFFFF; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; position: relative; overflow: hidden;">
                                    <svg width="100%" height="100%" viewBox="0 0 160 120">
                                        <path d="M 10 110 L 10 10 L 150 10 L 150 110" stroke="#BDBDBD" stroke-width="4" fill="none" stroke-linecap="square"/>
                                        <rect x="12" y="12" width="136" height="25" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <rect x="123" y="37" width="25" height="71" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <rect x="35" y="70" width="50" height="25" fill="#C2E7F3" stroke="#000" stroke-width="2"/>
                                        <g transform="translate(20, 14)"><rect width="20" height="20" fill="#fff" stroke="#000" stroke-width="1"/><circle cx="6" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="6" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="6" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/><circle cx="14" cy="14" r="3" fill="none" stroke="#000" stroke-width="1"/></g>
                                        <g transform="translate(68, 14)"><rect width="24" height="20" rx="2" fill="#fff" stroke="#000" stroke-width="1"/><rect x="2" y="2" width="20" height="12" rx="1" fill="none" stroke="#000" stroke-width="1"/><path d="M 12 14 L 12 18" stroke="#000" stroke-width="1"/></g>
                                        <text x="135" y="80" font-size="20" fill="#000" font-family="sans-serif" text-anchor="middle" dominant-baseline="central">*</text>
                                    </svg>
                                </div>
                                <span>Island</span>
                            </label>

                        </div>
                        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                            <button type="button" class="calc-submit-btn k-next-btn" style="width: auto; padding: 10px 30px;">Next <i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Kitchen Step 2: Measurements -->
                    <div id="kitchen-step-2" class="kitchen-step-content" style="display: none;">
                        <h3 style="text-align: center; color: white; margin-bottom: 25px;">Now review the measurements for accuracy</h3>
                        
                        <div id="k-measure-diagram" style="background: #FAF0E6; padding: 30px; border-radius: 12px; display: flex; justify-content: center; margin-bottom: 30px;">
                            <!-- Diagram drawn dynamically based on layout -->
                        </div>

                        <div style="background: rgba(244, 180, 26, 0.2); color: #F4B41A; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 20px; font-size: 14px;">
                            Standard size has been set for your convenience
                        </div>

                        <div id="k-measure-inputs" style="max-width: 400px; margin: 0 auto;">
                            <!-- Inputs added dynamically -->
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                            <button type="button" class="calc-submit-btn k-prev-btn" style="width: auto; padding: 10px 30px; background: rgba(255,255,255,0.1); color: white;"><i class="fa-solid fa-arrow-left"></i> Back</button>
                            <button type="button" class="calc-submit-btn k-next-btn" style="width: auto; padding: 10px 30px;">Next <i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Kitchen Step 3: Package -->
                    <div id="kitchen-step-3" class="kitchen-step-content" style="display: none;">
                        <h3 style="text-align: center; color: white; margin-bottom: 25px;">Pick your package</h3>
                        
                        <div class="calc-options-grid" style="grid-template-columns: repeat(2, 1fr); gap: 20px;">
                            <?php if(isset($calc_packages_data['kitchen'])): 
                                $is_first_kpkg = true;
                                foreach($calc_packages_data['kitchen'] as $kpkg): ?>
                            <label class="calc-option-card <?php echo $is_first_kpkg ? 'active' : ''; ?> k-package-card" style="padding: 20px; text-align: left; display: block;">
                                <input type="radio" name="k_package" value="<?php echo $kpkg['price_per_sqft']; ?>" <?php echo $is_first_kpkg ? 'checked' : ''; ?>>
                                <div style="font-size: 18px; font-weight: bold; margin-bottom: 10px; display: flex; align-items: center; justify-content: center;">
                                    <div class="k-pkg-radio" style="width:16px; height:16px; border-radius:50%; border:2px solid rgba(255,255,255,0.3); display:inline-block; margin-right:10px;"></div>
                                    <?php echo htmlspecialchars($kpkg['name']); ?>
                                </div>
                                <p style="display: none; font-size: 14px; margin-bottom: 15px; text-align: center; color: #F4B41A; font-weight: bold;">₹<?php echo number_format($kpkg['price_per_sqft']); ?>/RFT</p>
                                <div style="font-size: 13px; color: rgba(255,255,255,0.7); margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px;">
                                    <?php echo $kpkg['pdf_specs']; ?>
                                </div>
                            </label>
                            <?php $is_first_kpkg = false; endforeach; endif; ?>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                            <button type="button" class="calc-submit-btn k-prev-btn" style="width: auto; padding: 10px 30px; background: rgba(255,255,255,0.1); color: white;"><i class="fa-solid fa-arrow-left"></i> Back</button>
                            <button type="button" class="calc-submit-btn k-next-btn" style="width: auto; padding: 10px 30px;">Next <i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Kitchen Step 4: Add on -->
                    <div id="kitchen-step-4" class="kitchen-step-content" style="display: none;">
                        <h3 style="text-align: center; color: white; margin-bottom: 10px;">Add on</h3>
                        <p style="text-align: center; color: rgba(255,255,255,0.7); font-size: 13px; margin-bottom: 25px;">The prices are indicative only, actual cost will be provided after hardware finalization</p>
                        
                        <div class="calc-options-grid k-accessories-grid" style="grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            <?php 
                            foreach($calc_addons_data as $addon): 
                                if ($addon['category_slug'] === 'modular-kitchen'):
                            ?>
                            <label class="calc-checkbox-card" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: flex-start; cursor: pointer; transition: 0.3s;">
                                <input type="checkbox" name="k_accessories" value="<?php echo floatval($addon['percent_value']); ?>" data-name="<?php echo htmlspecialchars($addon['name']); ?>" style="margin-right: 15px; margin-top: 5px;">
                                <div>
                                    <div style="font-weight: bold; font-size: 14px; margin-bottom: 5px;"><?php echo htmlspecialchars($addon['name']); ?></div>
                                    <div style="color: #E74C3C; font-weight: bold; font-size: 13px; display: none;">₹<?php echo floatval($addon['percent_value']); ?>/sq ft</div>
                                </div>
                            </label>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                            <button type="button" class="calc-submit-btn k-prev-btn" style="width: auto; padding: 10px 30px; background: rgba(255,255,255,0.1); color: white;"><i class="fa-solid fa-arrow-left"></i> Back</button>
                            <button type="button" id="kitchen-calculate-btn" class="calc-submit-btn" style="width: auto; padding: 10px 30px;"><i class="fa-solid fa-calculator"></i> Get Quote</button>
                        </div>
                    </div>

                    </div> <!-- End kitchen-options -->
                </form>
            </div>
            
            <!-- Right Column: Results -->
            <div class="calc-results-col">
                <div class="calc-results-card" id="quotation-content">
                    <!-- PDF Only Header -->
                    <div id="pdf-header" style="display: none; text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0;">
                        <h2 style="margin: 0; color: #334C40; font-size: 24px;">Kalp Interior Studio</h2>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Official Quotation Estimate</p>
                    </div>

                    <h3 class="results-main-title">Your Estimated Cost Range</h3>
                    
                    <div class="results-highlight">
                        <p class="results-subtitle">Estimated Cost</p>
                        <h2 class="results-price" id="calc-total-range">₹0</h2>
                        <p class="results-sqft-price" id="calc-sqft-price" style="display: none;">(₹0 per sq.ft)</p>
                        <div class="results-sqft-badge" id="calc-sqft-badge" style="display: none;">
                            <i class="fa-regular fa-square-check"></i> <span id="badge-sqft-text">0 sq.ft</span>
                        </div>
                    </div>

                    <div class="results-breakdown" id="estimate-breakdown-section" style="display: none;">
                        <h4 class="breakdown-title">Estimate Breakdown</h4>
                        <ul class="breakdown-list">
                            <div id="dynamic-breakdown-list"></div>
                            <!-- Add-ons -->
                            <li id="li-addon-8" style="display: none; border-top: 1px dashed rgba(255,255,255,0.2); padding-top: 10px; margin-top: 10px; color: var(--accent-color);">
                                <span>+ Civil work</span>
                                <span id="bd-addon-8">₹0</span>
                            </li>
                            <li id="li-addon-10" style="display: none; color: var(--accent-color);">
                                <span>+ Flooring</span>
                                <span id="bd-addon-10">₹0</span>
                            </li>
                            <li id="li-addon-4" style="display: none; color: var(--accent-color);">
                                <span>+ Curtain/Soft Furnishing</span>
                                <span id="bd-addon-4">₹0</span>
                            </li>
                            <!-- Kitchen Accessories -->
                            <div id="kitchen-accessories-list" style="display: none; border-top: 1px dashed rgba(255,255,255,0.2); padding-top: 10px; margin-top: 10px; color: #F4B41A;">
                                <!-- Dynamically populated -->
                            </div>
                        </ul>
                        <div class="breakdown-total">
                            <span>Total Estimated Cost</span>
                            <span id="bd-total">₹0</span>
                        </div>
                    </div>

                    <button id="download-pdf-btn" type="button" class="results-pdf-btn" style="margin-bottom: 20px; width: 100%; padding: 12px 15px; background: linear-gradient(to right, #e8591c, #d13615); border: none; color: white; border-radius: 10px; cursor: pointer; display: none; align-items: center; justify-content: center; gap: 10px; transition: transform 0.2s ease, box-shadow 0.2s ease; box-shadow: 0 4px 10px rgba(209, 54, 21, 0.3);">
                        <svg width="34" height="34" viewBox="-4 -2 64 68" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink: 0;">
                            <!-- Document Base (White Fill) -->
                            <path d="M16 0 C 11.5 0, 8 3.5, 8 8 V 56 C 8 60.5, 11.5 64, 16 64 H 48 C 52.5 64, 56 60.5, 56 56 V 18 L 38 0 Z" fill="white"/>
                            <!-- Document Outline -->
                            <path d="M16 0 C 11.5 0, 8 3.5, 8 8 V 56 C 8 60.5, 11.5 64, 16 64 H 48 C 52.5 64, 56 60.5, 56 56 V 18 L 38 0 Z" stroke="black" stroke-width="4" stroke-linejoin="round"/>
                            <!-- Fold -->
                            <path d="M38 0 V 18 H 56" fill="white" stroke="black" stroke-width="4" stroke-linejoin="round"/>
                            <!-- Lines -->
                            <path d="M22 36 H42 M22 45 H42 M22 54 H42" stroke="black" stroke-width="4.5" stroke-linecap="round"/>
                            <!-- PDF Badge -->
                            <rect x="-4" y="14" width="42" height="18" rx="4" fill="#ff0000"/>
                            <!-- PDF Text -->
                            <text x="17" y="27.5" fill="white" font-size="15" font-family="Arial, sans-serif" font-weight="900" text-anchor="middle" letter-spacing="0.5">PDF</text>
                        </svg>
                        <span style="font-size: 20px; font-weight: 800; letter-spacing: 0.5px; font-family: sans-serif;">DOWNLOAD PDF</span>
                    </button>

                    <div class="results-whats-included">
                        <div class="wi-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
                        <div class="wi-text">
                            <strong>What's Included?</strong>
                            <p>Complete interior design, materials, labour, installation & project management.</p>
                        </div>
                    </div>

                    <a href="contact.php" class="results-cta" style="background-image: linear-gradient(rgba(15, 61, 100, 0.8), rgba(15, 61, 100, 0.9)), url('assets/images/cta_bg.png'); background-size: cover; background-position: center;">
                        <div class="cta-icon"><i class="fa-regular fa-calendar-check"></i></div>
                        <div class="cta-text">
                            <strong>Book a Free Consultation</strong>
                            <span>Speak with our design expert</span>
                        </div>
                        <i class="fa-solid fa-chevron-right cta-arrow"></i>
                    </a>
                </div>
            </div>
            
        </div>
        
        <!-- Calculator Footer Badges -->
        <div class="calc-footer-badges">
            <div class="cf-badge">
                <i class="fa-solid fa-medal"></i>
                <div class="cf-text">
                    <strong>10+ Years Experience</strong>
                    <span>Expert interior designers</span>
                </div>
            </div>
            <div class="cf-badge">
                <i class="fa-regular fa-clock"></i>
                <div class="cf-text">
                    <strong>On-time Delivery</strong>
                    <span>We value your time</span>
                </div>
            </div>
            <div class="cf-badge">
                <i class="fa-solid fa-certificate"></i>
                <div class="cf-text">
                    <strong>Quality Assurance</strong>
                    <span>Premium materials only</span>
                </div>
            </div>
            <div class="cf-badge">
                <i class="fa-solid fa-hand-holding-heart"></i>
                <div class="cf-text">
                    <strong>Custom Designs</strong>
                    <span>Tailored to your lifestyle</span>
                </div>
            </div>
        </div>

    </div>
<!-- Hidden PDF Export Template -->
<?php
if(!empty($calc_settings['pdf_template_html'])) {
    echo $calc_settings['pdf_template_html'];
} else {
?>
<!-- Hidden PDF Export Template Fallback -->
<div id="pdf-export-template" style="display: none; width: 794px; height: auto; font-family: 'Inter', sans-serif; background: #ffffff; color: #333333; overflow: hidden; box-sizing: border-box;">
    <!-- Default template will be here if DB fails, but we assume DB has it now -->
    <div style="padding: 40px; text-align: center;">PDF Template Missing from Database</div>
</div>
<?php } ?>

<!-- Hidden Material Specs Data -->
<div id="specs-1200" style="display:none;">
    <strong>Material Composition:</strong> 90% Non-Branded + 10% Branded<br>
    <strong>Plywood:</strong> Euro, Grain Touch, Wood Touch, Mount Black, Tree Lab (MDF)<br>
    <strong>Laminate:</strong> 0.8 mm &ndash; Trends, Skydecor, Black Cobra<br>
    <strong>Adhesive:</strong> Fevicol SH<br>
    <strong>Hardware:</strong> Godrej, Ozone, Equivalent Brands<br>
    <strong>Handles:</strong> Local Aluminium<br>
    <strong>Locks:</strong> Godrej<br>
    <strong>Kitchen:</strong> Laminate Finish, Wire Basket System, Godrej Accessories<br>
    <strong>Sink:</strong> Levistone<br>
    <strong>Faucets:</strong> Jaquar, Parryware, Cera<br>
    <strong>Paint:</strong> Asian Paints Tractor Emulsion (Matte)<br>
    <strong>Switches:</strong> Anchor Roma, Siya, Goldmedal<br>
    <strong>Wire:</strong> Polycab<br>
    <strong>Lighting:</strong> Orient, Surya, Nortus, Nortek<br>
    <strong>Mirror:</strong> Saint-Gobain<br>
    <strong>Accessories:</strong> Ozone<br>
    <div style="margin-top: 10px;"><strong>Includes:</strong> Bed, TV Unit, Dressing/Study, Wardrobes, False Ceiling, Lighting, Door Panelling, Wall Panelling, Side Tables, Modular Kitchen</div>
    <div style="margin-top: 5px;"><strong>Does Not Include:</strong> Hydraulic Bed, Fans & Appliances, Sofa Set, Dining Table & Chairs, Home Automation</div>
</div>

<div id="specs-1450" style="display:none;">
    <strong>Material Composition:</strong> 10% non branded + 90% branded<br>
    <strong>Plywood:</strong> Century Sainik 710, Green Ecotec, Austin, Green HDHMR<br>
    <strong>Laminate:</strong> 0.8 mm &amp; 1.0 mm<br>
    <strong>Brands:</strong> Advance, Century Laminates, Greenlam, Skydecor<br>
    <strong>Adhesive:</strong> Fevicol MR<br>
    <strong>Hardware:</strong> Ebco, Hettich, Godrej<br>
    <strong>Handles:</strong> Ebco<br>
    <strong>Locks:</strong> Godrej, Hettich<br>
    <strong>Kitchen:</strong> Acrylic Finish, Tandem Drawers, Hettich<br>
    <strong>Sink:</strong> Carysil<br>
    <strong>Faucets:</strong> Jaquar, Grohe (if required)<br>
    <strong>Paint:</strong> Asian Paints Premium Emulsion (Matte)<br>
    <strong>Switches:</strong> Havells, Legrand, Crabtree<br>
    <strong>Wire:</strong> Polycab, KEI<br>
    <strong>Lighting:</strong> Havells, Polycab<br>
    <strong>Mirror:</strong> Saint-Gobain<br>
    <strong>Accessories:</strong> Hettich<br>
    <div style="margin-top: 10px;"><strong>Includes:</strong> Hydraulic Bed, TV Unit, Dressing/Study, Wardrobes, False Ceiling, Lighting, Door Panelling, Wall Panelling, Side Tables, Modular Kitchen, Sofa Set, Dining Table</div>
    <div style="margin-top: 5px;"><strong>Does Not Include:</strong> Fans & Appliances, Mattress, Home Automation</div>
</div>

<div id="specs-1650" style="display:none;">
    <strong>Material Composition:</strong> 100% Branded Materials<br>
    <strong>Plywood:</strong> CenturyPly Club Prime, Green Club 700, Century Architect Ply, Green HDHMR<br>
    <strong>Laminate:</strong> 1.0 mm &ndash; Greenlam 1.25 mm, Century Laminates Premium, Royale Touche<br>
    <strong>Adhesive:</strong> Fevicol Hyper<br>
    <strong>Hardware:</strong> Blum, Hettich Premium<br>
    <strong>Handles:</strong> Hafele, Hettich<br>
    <strong>Locks:</strong> Godrej Premium<br>
    <strong>Kitchen:</strong> Acrylic/DUCO/PU Finish, Tandem Drawers, Hettich<br>
    <strong>Sink:</strong> Franke, Carysil Quartz<br>
    <strong>Faucets:</strong> Kohler, Grohe (if required)<br>
    <strong>Paint:</strong> Asian Paints Royale (Matte)<br>
    <strong>Switches:</strong> Legrand Arteor, Schneider Electric<br>
    <strong>Wire:</strong> Polycab FRLS, RR Kabel<br>
    <strong>Lighting:</strong> Havells, Polycab, Philips<br>
    <strong>Mirror:</strong> Saint-Gobain<br>
    <strong>Accessories:</strong> Blum, Hafele Premium<br>
    <div style="margin-top: 10px;"><strong>Includes:</strong> Fans, Hydraulic Bed, TV Unit, Dressing/Study, Wardrobes, False Ceiling, Lighting, Door Panelling, Wall Panelling, Side Tables, Modular Kitchen, Sofa Set, Dining Table, Chimney</div>
    <div style="margin-top: 5px;"><strong>Does Not Include:</strong> Appliances, Mattress, Home Automation</div>
</div>

</section>

<!-- Lead Capture Modal -->
<style>
#calc-lead-modal .login-card-wrapper {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 440px;
    padding: 0 20px;
}
#calc-lead-modal .shield-icon-container {
    position: absolute;
    top: -42px;
    left: 50%;
    transform: translateX(-50%);
    width: 84px;
    height: 84px;
    background: #111;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #EAB136;
    box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    z-index: 15;
}
#calc-lead-modal .shield-icon-container i {
    color: #EAB136;
    font-size: 32px;
}
#calc-lead-modal .login-card {
    background: #FFFFFF;
    border-radius: 20px;
    padding: 60px 40px 40px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
    position: relative;
    border-top: 3px solid #EAB136;
    text-align: left;
}
#calc-lead-modal .login-header {
    text-align: center;
    margin-bottom: 25px;
}
#calc-lead-modal .login-header h1 {
    font-family: 'League Spartan', sans-serif;
    font-size: 36px;
    color: #1E2723;
    margin-bottom: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}
#calc-lead-modal .login-header h1 span {
    color: #EAB136;
}
#calc-lead-modal .login-header p {
    color: #66756C;
    font-size: 14px;
    line-height: 1.6;
}
#calc-lead-modal .form-group {
    margin-bottom: 20px;
}
#calc-lead-modal .form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #1E2723;
    margin-bottom: 8px;
}
#calc-lead-modal .input-wrapper {
    position: relative;
}
#calc-lead-modal .input-wrapper .input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #66756C;
    font-size: 16px;
    z-index: 2;
}
#calc-lead-modal .form-control {
    width: 100%;
    padding: 14px 16px 14px 45px;
    background: #F6F6F6;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    font-size: 14px;
    color: #1E2723;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
    box-sizing: border-box;
}
#calc-lead-modal .form-control:focus {
    outline: none;
    background: #FFFFFF;
    border-color: #EAB136;
    box-shadow: 0 0 0 4px rgba(234, 177, 54, 0.1);
}
#calc-lead-modal .btn-login {
    width: 100%;
    padding: 14px;
    background: #EAB136;
    color: #1E2723;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 12px rgba(234, 177, 54, 0.3);
    font-family: 'Inter', sans-serif;
    margin-top: 10px;
}
#calc-lead-modal .btn-login:hover {
    transform: translateY(-2px);
    background: #D69E2B;
    box-shadow: 0 6px 15px rgba(234, 177, 54, 0.4);
}
#calc-lead-modal #calc-lead-close {
    width: 100%;
    padding: 10px;
    background: transparent;
    color: #66756C;
    border: none;
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
    text-decoration: underline;
    font-family: 'Inter', sans-serif;
}
#calc-lead-modal #calc-lead-close:hover {
    color: #1E2723;
}
</style>
<div id="calc-lead-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
    <div class="login-card-wrapper">
        <div class="shield-icon-container">
            <i class="fa-solid fa-calculator"></i>
        </div>
        
        <div class="login-card">
            <div class="login-header">
                <h1>Get <span>Estimate</span></h1>
                <p>Please provide your details to view<br>the detailed cost estimate.</p>
            </div>
            
            <form id="calc-lead-form">
                <div id="lead-details-section">
                    <div class="form-group">
                        <label for="lead-name">Name *</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-user input-icon"></i>
                            <input type="text" id="lead-name" class="form-control" placeholder="Enter your full name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="lead-contact">Contact Number *</label>
                        <div class="input-wrapper">
                            <i class="fa-brands fa-whatsapp input-icon" style="color: #25D366; font-size: 1.2em;"></i>
                            <input type="tel" id="lead-contact" class="form-control" placeholder="Enter your phone number" required pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="lead-location">Location *</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-location-dot input-icon"></i>
                            <input type="text" id="lead-location" class="form-control" placeholder="Enter your city or area" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login" id="lead-submit-btn">
                        <i class="fa-solid fa-paper-plane"></i>
                        Send OTP
                    </button>
                    <button type="button" id="calc-lead-close">Cancel</button>
                </div>

                <div id="otp-section" style="display: none;">
                    <div class="form-group">
                        <label for="lead-otp">Enter OTP *</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-key input-icon"></i>
                            <input type="text" id="lead-otp" class="form-control" placeholder="Enter 6-digit OTP" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div id="otp-error" style="color: #E74C3C; font-size: 13px; margin-top: 8px; display: none;">Invalid OTP</div>
                        <div id="otp-success" style="color: #27AE60; font-size: 13px; margin-top: 8px; display: none;">OTP Sent!</div>
                    </div>
                    
                    <button type="button" class="btn-login" id="verify-otp-btn">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        Verify & View Estimate
                    </button>
                    <button type="button" id="otp-back-btn" style="width: 100%; padding: 10px; background: transparent; color: #66756C; border: none; cursor: pointer; font-size: 14px; margin-top: 10px; text-decoration: underline; font-family: 'Inter', sans-serif;">Change Number</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Javascript for logic -->
<script>
window.CALC_CONFIG = {
    forceCategory: "<?php echo isset($force_category) ? addslashes($force_category) : ''; ?>",
    settings: <?php echo json_encode($calc_settings); ?>,
    packages: <?php echo json_encode($calc_packages_data); ?>,
    breakdown: {
        // Residential
        res_furniture: <?php echo $calc_settings['bd_residential_furniture'] ?? 28.5; ?>,
        res_wardrobes: <?php echo $calc_settings['bd_residential_wardrobes'] ?? 20.4; ?>,
        res_kitchen: <?php echo $calc_settings['bd_residential_kitchen'] ?? 15.5; ?>,
        res_false_ceiling: <?php echo $calc_settings['bd_residential_false_ceiling'] ?? 9.7; ?>,
        res_electrical: <?php echo $calc_settings['bd_residential_electrical'] ?? 8.9; ?>,
        res_design: <?php echo $calc_settings['bd_residential_design'] ?? 7.0; ?>,
        res_paint: <?php echo $calc_settings['bd_residential_paint'] ?? 7.5; ?>,
        // Commercial
        com_furniture: <?php echo $calc_settings['bd_commercial_furniture'] ?? 28.5; ?>,
        com_wardrobes: <?php echo $calc_settings['bd_commercial_wardrobes'] ?? 20.4; ?>,
        com_kitchen: <?php echo $calc_settings['bd_commercial_kitchen'] ?? 15.5; ?>,
        com_false_ceiling: <?php echo $calc_settings['bd_commercial_false_ceiling'] ?? 9.7; ?>,
        com_electrical: <?php echo $calc_settings['bd_commercial_electrical'] ?? 8.9; ?>,
        com_design: <?php echo $calc_settings['bd_commercial_design'] ?? 7.0; ?>,
        com_paint: <?php echo $calc_settings['bd_commercial_paint'] ?? 7.5; ?>
    },
    breakdownData: <?php echo json_encode($calc_breakdowns_data); ?>,
    appended_pdf_path: "<?php echo addslashes($calc_settings['appended_pdf_path'] ?? ''); ?>"
};
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="assets/js/calculator.js?v=<?php echo time(); ?>"></script>
