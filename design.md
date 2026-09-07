# Design Specifications: Kalp Interior Studio

## 1. Brand Guidelines
**Logo & Name:** Kalp Interior Studio
**Primary Color:** Deep Forest Green (approx. `#2c473e`) - Used for primary backgrounds and strong accents.
**Accent Color:** Golden Yellow (approx. `#eab332`) - Used for buttons, highlighted text, and icons.
**Text Colors:**
- Dark text for light backgrounds: `#333333`
- Light text for dark backgrounds: `#ffffff`
- Muted text: `#666666`
**Background Colors:** 
- Off-white / Light Gray (`#f8f8f8`)
- Solid White (`#ffffff`)

**Typography:**
- Primary Font (Sans-Serif): 'Inter' or 'Outfit' for modern, clean body text and standard headings.
- Accent Font (Serif, Italic): 'Playfair Display' or similar for the golden italicized accent phrases (e.g., *Modern Luxury*, *Your Dream Home*).

## 2. Layout Structure (Top to Bottom)
1. **Top Bar:** Contact number, email address, and social media links.
2. **Navbar:** Logo on left, navigation links (Home, Services, Projects, Blog, About Us, Contact Us) in center, CTA button ("Get A Quote") on right.
3. **Hero Section:** Split layout. Left: Headline with accent text, description, "Get Started" button. Right: Large feature image with a circular "Get In Touch" badge and video play icon. Background is Deep Forest Green.
4. **Features & Stats:** 
   - Three key feature highlights (Reasonable Prices, Timely Project Delivery, Professional Team) with icons.
   - "18 Years of Experience" large graphic overlapping with "Turning *Your Dream Home* into Reality" text and stats (250+ Projects, etc.).
5. **Services Section:** "Explore Our Services". Horizontal scroll or grid of service cards (Architectural Design, Interior Design, Hospitality Design). Each card has an image and an arrow icon.
6. **Scrolling Marquee:** A continuous horizontal banner with yellow background and black text showing keywords (Residential Design, Commercial Design, etc.).
7. **Work Process:** Four steps (Survey, Design, Construct, Handover) with connected circular icons.
8. **Projects Section:** "Explore *Our Portfolio*". Filter pills (All, Interior, Exterior, etc.) and large project cards with badges.
9. **Awards Section:** Grid showing various awards (Innovative Design Award, etc.) with laurel wreath icons.
10. **Before & After:** Interactive or side-by-side layout showing a space before and after transformation.
11. **Testimonials:** Client photo and quote with a rating. Navigation arrows for slider.
12. **Team Section:** "Meet Our *Expert Team*". Photos of team members with names, titles, and social links on hover.
13. **Contact Section:** Split layout. Left: "Get Your *Free Quote Today!*" form. Right: Deep Forest Green card with Address, Contact info, Open Time, and Social links.
14. **Footer:** Simple copyright footer.

## 3. SEO & Responsiveness
- **SEO:** Use proper HTML5 semantic tags (`<header>`, `<section>`, `<article>`, `<footer>`). Ensure single `<h1>` on the page. Add meta title and description in the PHP header. Use alt text for all images.
- **Responsiveness:** Use Flexbox and CSS Grid. Media queries for tablets (`< 768px`) and mobile (`< 480px`) to stack columns, resize fonts, and adjust padding.

## 4. Folder Structure (PHP + CSS)
```text
kalp-interior-studio/
├── assets/
│   ├── css/
│   │   ├── reset.css
│   │   ├── variables.css
│   │   └── style.css
│   ├── images/
│   │   ├── (placeholder images for all sections)
│   ├── js/
│   │   └── main.js
├── includes/
│   ├── header.php
│   └── footer.php
├── index.php
└── design.md
```
