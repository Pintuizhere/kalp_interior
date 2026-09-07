<?php
require 'admin/config/db.php';

$html = <<<HTML
<div id="pdf-export-template" style="display: none; width: 794px; min-height: 1123px; font-family: 'Inter', sans-serif; background: #ffffff; color: #333333; box-sizing: border-box;">
    <div style="display: flex; width: 100%; height: 100%; min-height: 1123px;">
        <!-- Left Sidebar -->
        <div style="width: 32%; flex-shrink: 0; display: flex; flex-direction: column; background: #ffffff; height: 100%; min-height: 1123px;">
            <!-- Brown Top Section -->
            <div style="background-color: #a49375; color: #ffffff; padding: 40px 30px; box-sizing: border-box; flex-grow: 1;">
                <!-- Logo -->
                <div style="margin-bottom: 50px;">
                    <img src="assets/images/logo.png" style="max-width: 160px; height: auto; filter: brightness(0) invert(1);" alt="KALP Logo">
                </div>

                <div style="margin-bottom: 25px; font-size: 10px; line-height: 1.6;">
                    Online Quotation.<br>
                    info@kalpinteriors.com
                </div>

                <div style="margin-bottom: 35px; font-size: 10px; line-height: 1.6;">
                    KALP INTERIOR DESIGN STUDIO.<br>
                    ISM CHOWK ROAD, OPP<br>
                    SR.DAV SCHOOL , PUNDAG,<br>
                    RANCHI - 834004
                </div>

                <div style="margin-bottom: 35px;">
                    <div style="font-weight: 600; font-size: 10px; margin-bottom: 15px; border-top: 1px solid rgba(255,255,255,0.5); padding-top: 15px; width: 40px;">CONTACT:</div>
                    <div style="font-size: 10px; margin-bottom: 12px;">
                        <strong>Office :</strong><br>
                        +91 9472745288
                    </div>
                    <div style="font-size: 10px;">
                        <strong>Studio Head :</strong><br>
                        +91 9234772288
                    </div>
                </div>

                <div>
                    <div style="font-weight: 600; font-size: 10px; margin-bottom: 10px;">BUDGET DISCLAIMER</div>
                    <div style="font-size: 8.5px; line-height: 1.5; opacity: 0.9;">
                        The cost mentioned in this quotation is an estimated budget based on the current project scope and preliminary requirements. It is not the final project cost. The actual project budget will be finalized only after the design is approved, detailed measurements are completed, material selections are confirmed, and the final BOQ (Bill of Quantities) is prepared. Any changes in design, specifications, materials, finishes, or scope of work may result in a revision of the final project cost.
                    </div>
                </div>
            </div>
            <!-- Clients Bottom Section -->
            <div style="background-color: #ffffff; padding: 25px 30px; box-sizing: border-box; text-align: center;">
                <img src="assets/images/our_clients_collage.png" style="max-width: 100%; height: auto;" alt="Our Clients">
            </div>
        </div>
        
        <!-- Right Main Content -->
        <div style="width: 68%; padding: 30px 40px; box-sizing: border-box; display: flex; flex-direction: column; position: relative; min-height: 1123px;">
            
            <!-- Header (Title & Date/Time) -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
                <div style="font-size: 26px; font-weight: 600; color: #a49375; line-height: 1.2; letter-spacing: 2px;">
                    ONLINE<br>QUOTATION
                </div>
                <div style="text-align: right; font-size: 9px; color: #666666; font-weight: 500; line-height: 1.8;">
                    <div style="letter-spacing: 1px;">DATE : <span id="pdf-export-date"></span></div>
                    <div style="letter-spacing: 1px;">TIME : <span id="pdf-export-time"></span></div>
                </div>
            </div>

            <!-- Project Details -->
            <div style="margin-bottom: 20px;">
                <div style="font-size: 10px; font-weight: 700; color: #a49375; letter-spacing: 1.5px; margin-bottom: 10px; text-transform: uppercase;">Project Details</div>
                <table style="width: 100%; font-size: 10px; color: #333333; border-collapse: collapse;">
                    <tbody>
                        <tr><td style="padding: 3px 0; width: 40%; font-weight: 600;">Property Category</td><td style="padding: 3px 0;" id="pdf-category"></td></tr>
                        <tr><td style="padding: 3px 0; font-weight: 600;">Specific Type</td><td style="padding: 3px 0;" id="pdf-type"></td></tr>
                        <tr><td style="padding: 3px 0; font-weight: 600;">Design Style</td><td style="padding: 3px 0;" id="pdf-style"></td></tr>
                        <tr><td style="padding: 3px 0; font-weight: 600;">Selected Package</td><td style="padding: 3px 0;" id="pdf-package"></td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Cost Breakdown -->
            <div style="background: #fafafa; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                <div style="font-size: 10px; font-weight: 700; color: #a49375; letter-spacing: 1.5px; margin-bottom: 10px; text-transform: uppercase;">Cost Breakdown</div>
                
                <table style="width: 100%; font-size: 10px; color: #333333; border-collapse: collapse;">
                    <tbody id="pdf-breakdown-list">
                        <!-- Standard Items -->
                        <tr><td style="padding: 5px 0;">TV Unit, Crockery, Vanity &amp; Other Furniture</td><td style="padding: 5px 0; text-align: right;" id="pdf-bd-furniture"></td></tr>
                        <tr><td style="padding: 5px 0;">Wardrobes &amp; Storage</td><td style="padding: 5px 0; text-align: right;" id="pdf-bd-wardrobes"></td></tr>
                        <tr><td style="padding: 5px 0;">Modular Kitchen</td><td style="padding: 5px 0; text-align: right;" id="pdf-bd-kitchen"></td></tr>
                        <tr><td style="padding: 5px 0;">False Ceiling</td><td style="padding: 5px 0; text-align: right;" id="pdf-bd-false-ceiling"></td></tr>
                        <tr><td style="padding: 5px 0;">Electrical &amp; Lighting</td><td style="padding: 5px 0; text-align: right;" id="pdf-bd-electrical"></td></tr>
                        <tr><td style="padding: 5px 0;">Paint &amp; Wall Finishes</td><td style="padding: 5px 0; text-align: right;" id="pdf-bd-paint"></td></tr>
                        <tr><td style="padding: 5px 0;">Decorative Lights &amp; Accessories</td><td style="padding: 5px 0; text-align: right;" id="pdf-bd-decorative"></td></tr>
                        <tr><td style="padding: 5px 0;">Design, Project Management &amp; Site Supervision</td><td style="padding: 5px 0; text-align: right;" id="pdf-bd-design"></td></tr>
                        
                        <!-- Add-ons -->
                        <tr id="pdf-row-addon-8" style="display:none;"><td style="padding: 5px 0; color: #a49375;">+ Study / Home Office</td><td style="padding: 5px 0; text-align: right; color: #a49375;" id="pdf-bd-addon-8"></td></tr>
                        <tr id="pdf-row-addon-10" style="display:none;"><td style="padding: 5px 0; color: #a49375;">+ Balcony Design</td><td style="padding: 5px 0; text-align: right; color: #a49375;" id="pdf-bd-addon-10"></td></tr>
                        <tr id="pdf-row-addon-4" style="display:none;"><td style="padding: 5px 0; color: #a49375;">+ Deep Cleaning &amp; Handover</td><td style="padding: 5px 0; text-align: right; color: #a49375;" id="pdf-bd-addon-4"></td></tr>
                    </tbody>
                    <tbody id="pdf-kitchen-accessories-list"></tbody>
                </table>
                
                <div style="border-top: 1px solid #e0e0e0; margin-top: 10px; padding-top: 10px; display: flex; justify-content: space-between; align-items: center; font-size: 12px; font-weight: 700;">
                    <div style="color: #215c38;">Total Estimated Cost</div>
                    <div style="color: #215c38;" id="pdf-cost-total"></div>
                </div>
            </div>

            <!-- Material Specifications -->
            <div style="margin-bottom: auto;">
                <div style="font-size: 10px; font-weight: 700; color: #a49375; letter-spacing: 1.5px; margin-bottom: 8px; text-transform: uppercase;" id="pdf-material-specs-title">Essential Material Specification</div>
                <div id="pdf-material-specs" style="font-size: 8px; line-height: 1.5; color: #444444;">
                    <!-- Inserted by JS -->
                </div>
            </div>

            <!-- Client Info & Footer -->
            <div style="margin-top: 20px; font-size: 10px; color: #333333;">
                <div style="margin-bottom: 3px;"><strong>Name:</strong> <span id="pdf-user-name"></span></div>
                <div style="margin-bottom: 3px;"><strong>Contact Number:</strong> <span id="pdf-user-phone"></span></div>
                <div style="margin-bottom: 12px;"><strong>Location:</strong> <span id="pdf-user-location"></span></div>
                
                <div style="font-size: 8px; font-style: italic; color: #777777; line-height: 1.4; margin-bottom: 8px;">
                    This quotation has been prepared based on your requirements. Kindly review the scope of work, specifications, and pricing. We look forward to bringing your vision to life.
                </div>
                <div style="font-size: 9px; font-weight: 600; color: #555555;">
                    Kalp Interior Design Studio
                </div>
            </div>
            
        </div>
    </div>
</div>
HTML;

$stmt = $conn->prepare("UPDATE calculator_settings SET setting_value = ? WHERE setting_key = 'pdf_template_html'");
$stmt->bind_param("s", $html);
if($stmt->execute()) {
    echo "Updated successfully!";
} else {
    echo "Error: " . $conn->error;
}
