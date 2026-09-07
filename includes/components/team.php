<!-- Meet Our Team Section -->
<style>
  .team-container {
    --bg-team: #111111;
    --panel: #121212;
    --border: #232323;
    --border-hover: var(--accent-color, #a9793a);
    --gold: var(--accent-color, #c99a44);
    --gold-bright: #e3ba72;
    --text: #f2f2f0;
    --text-soft: #9c9c9c;
    --text-mute: #6a6a6a;
    background: var(--bg-team);
    color: var(--text);
    font-family: var(--font-primary, 'Inter', sans-serif);
  }

  .team-container * { box-sizing: border-box; }

  .team-container .team-section {
    position: relative;
    padding: 110px 8vw 130px;
    overflow: hidden;
    max-width: 1400px;
    margin: 0 auto;
    background-color: #111111 !important;
  }

  /* soft ambient glow, echoes the warmth of the gold accent without adding a new motif */
  .team-container .team-section::before {
    content:"";
    position:absolute;
    top:-200px; left:50%;
    width:900px; height:500px;
    background: radial-gradient(ellipse at center, rgba(201,154,68,0.10), transparent 70%);
    transform: translateX(-50%);
    pointer-events:none;
  }

  .team-container .team-header {
    position: relative;
    max-width: 620px;
    margin: 0 auto 76px;
    text-align: center;
  }

  .team-container .team-header .rule {
    width: 46px;
    height: 2px;
    background: var(--gold);
    margin: 0 auto 26px;
  }

  .team-container .team-header h2 {
    font-family: var(--font-primary, 'Archivo Black', sans-serif);
    font-weight: 700;
    font-size: clamp(2.1rem, 4vw, 3rem);
    line-height: 1.05;
    letter-spacing: -0.01em;
    margin: 0 0 20px;
    text-transform: uppercase;
    color: white;
  }



  .team-container .team-grid {
    position: relative;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
  }

  .team-container .team-card {
    border: 1px solid var(--border);
    background: var(--panel);
    padding: 20px 20px 26px;
    transition: border-color 0.35s ease, box-shadow 0.35s ease, transform 0.35s ease;
  }

  .team-container .team-card:hover {
    border-color: var(--border-hover);
    box-shadow: 0 0 0 1px rgba(201,154,68,0.15), 0 24px 40px -28px rgba(201,154,68,0.35);
    transform: translateY(-4px);
  }

  .team-container .photo-frame {
    border: 1px solid var(--border);
    padding: 8px;
    margin-bottom: 22px;
    transition: border-color 0.35s ease;
  }

  .team-container .team-card:hover .photo-frame {
    border-color: rgba(201,154,68,0.4);
  }

  .team-container .photo-frame .photo {
    aspect-ratio: 4 / 5;
    width: 100%;
    overflow: hidden;
    background: #1a1a1a;
  }

  .team-container .photo-frame img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    filter: grayscale(85%) contrast(1.02);
    transition: filter 0.5s ease, transform 0.6s ease;
  }

  .team-container .team-card:hover .photo-frame img {
    filter: grayscale(0%) contrast(1.02);
    transform: scale(1.035);
  }

  .team-container .team-card h3 {
    font-family: var(--font-primary, 'Archivo Black', sans-serif);
    font-weight: 700;
    text-transform: uppercase;
    font-size: 1.28rem;
    letter-spacing: -0.005em;
    margin: 0 0 6px;
    color: white;
  }

  .team-container .team-card .role {
    display: block;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 16px;
  }



  .team-container .contact-row {
    display: flex;
    gap: 14px;
    border-top: 1px solid var(--border);
    padding-top: 16px;
  }

  .team-container .contact-row a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    color: var(--text-mute);
    border: 1px solid var(--border);
    text-decoration: none;
    transition: color 0.25s ease, border-color 0.25s ease;
  }

  .team-container .contact-row a:hover,
  .team-container .contact-row a:focus-visible {
    color: var(--gold-bright);
    border-color: var(--gold);
  }

  .team-container .contact-row svg { width: 15px; height: 15px; }

  .team-container a:focus-visible, 
  .team-container .team-card:focus-visible {
    outline: 2px solid var(--gold);
    outline-offset: 2px;
  }

  @media (max-width: 640px) {
    .team-container .team-section { padding: 80px 6vw 90px; }
    .team-container .team-grid { grid-template-columns: 1fr; gap: 26px; }
    .team-container .team-card:nth-child(2n) { margin-top: 0; }
  }

  @media (prefers-reduced-motion: reduce) {
    .team-container .team-card, 
    .team-container .photo-frame, 
    .team-container .photo-frame img, 
    .team-container .contact-row a { transition: none; }
    .team-container .team-card:hover { transform: none; }
  }
</style>

<div class="team-container" style="background: #111111; border-top: 1px solid rgba(255,255,255,0.05);">
  <section class="team-section">
    <div class="team-header">
      <div class="rule"></div>
      <h2>Meet Our Team</h2>

    </div>

    <div class="team-grid">
      <?php
      $team_query = "SELECT * FROM team_members WHERE status='active' ORDER BY display_order ASC, created_at DESC";
      $team_result = $conn->query($team_query);
      if ($team_result && $team_result->num_rows > 0) {
          while ($member = $team_result->fetch_assoc()) {
              $img_src = !empty($member['image']) ? 'uploads/team/' . htmlspecialchars($member['image']) : 'https://ui-avatars.com/api/?name='.urlencode($member['name']);
      ?>
      <article class="team-card" tabindex="0">
        <div class="photo-frame">
          <div class="photo">
            <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
          </div>
        </div>
        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
        <span class="role"><?php echo htmlspecialchars($member['role']); ?></span>

        <div class="contact-row">
          <?php if(!empty($member['email'])): ?>
          <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" aria-label="Email <?php echo htmlspecialchars($member['name']); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18v12H3z"/><path d="M3 7l9 6 9-6"/></svg>
          </a>
          <?php endif; ?>
          <?php if(!empty($member['whatsapp'])): ?>
          <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $member['whatsapp']); ?>" aria-label="WhatsApp <?php echo htmlspecialchars($member['name']); ?>" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.7-1.2A9 9 0 1 0 12 3z"/><path d="M8.5 8.7c.3-.6 1-.6 1.3 0l.6 1.2c.2.4.1.9-.2 1.2l-.4.4c.4.9 1.3 1.8 2.2 2.2l.4-.4c.3-.3.8-.4 1.2-.2l1.2.6c.6.3.6 1 0 1.3-1.5.9-3.4.5-4.9-.9-1.4-1.5-1.8-3.4-.9-4.9"/></svg>
          </a>
          <?php endif; ?>
        </div>
      </article>
      <?php 
          }
      } else {
          echo "<p style='color: var(--text-soft); grid-column: 1 / -1; text-align: center;'>Our team is currently being updated. Check back soon!</p>";
      }
      ?>
    </div>
  </section>
</div>
