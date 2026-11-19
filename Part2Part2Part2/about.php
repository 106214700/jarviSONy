<?php
// about.php | owner: Quan | Part 2 page template (see guide Part A6)
$page_title       = "About";
$body_class       = "page-about";
$active_page      = "about";
$meta_description = 'Meet team jarviSONy: members, contributions, quotes, group photo and fun facts.';
$extra_head = <<<'HTML'
<style>
    /* apply for class page-about and about figure figcaption only, not body or other elements */
    .page-about .about-figure figcaption {
      font-style: italic;
    }
  </style>
HTML;
require_once __DIR__ . '/functions.php';
include __DIR__ . '/header.inc';
?>
<?php /* TODO(Quan): load the member contributions and fun facts from the `about` table. */ ?>
    <div class="container stack">
      <h1>About our team</h1>

      <!-- SECTION 1: NESTED LIST for group details -->
      <section aria-labelledby="about-group">
        <!-- Inline CSS example as required by contract -->
        <h2 id="about-group" style="margin-top: 2rem;">Our group</h2>
        <div class="about-group">
          <ul>
            <li>Group name: jarviSONy
              <ul>
                <li>Class day and time: Saturday Morning</li>
              </ul>
            </li>
          </ul>
        </div>
      </section>

      <!-- SECTION 2: DEFINITION LIST for each member -->
      <section aria-labelledby="about-members">
        <h2 id="about-members">Contributions</h2>
        <dl class="about-members">
          <!-- FIRST MEMBER: VINH (TEAM LEADER) -->
          <dt>Bùi Thế Vinh <span class="about-id">106214700</span></dt>
          <dd><strong>Contribution:</strong> Team leader. Built the home page (SCRUM-12), the common navigation menu and
            footer (SCRUM-13), the shared stylesheet and the final merge.</dd>
          <dd>
            <blockquote lang="vi">
              <p>Có công mài sắt, có ngày nên kim.</p>
            </blockquote>
            <p class="about-quote-en" lang="en">English: <q>With enough grinding, an iron bar becomes a needle.</q>
              <cite>Vietnamese proverb</cite>
            </p>
          </dd>

          <!-- SECOND MEMBER: NGUYEN -->
          <dt>Nguyễn Khoa Nguyên <span class="about-id">106392415</span></dt>
          <dd><strong>Contribution:</strong> Built the Jobs page (SCRUM-14) with two realistic job descriptions.</dd>
          <dd>
            <blockquote lang="vi">
              <p>Đi một ngày đàng, học một sàng khôn.</p>
            </blockquote>
            <p class="about-quote-en" lang="en">English: <q>A day of travel teaches a basketful of wisdom.</q>
              <cite>Vietnamese proverb</cite>
            </p>
          </dd>

          <!-- THIRD MEMBER: DUNG -->
          <dt>Phạm Trần Quốc Dũng <span class="about-id">106396488</span></dt>
          <dd><strong>Contribution:</strong> Built the Apply page (SCRUM-15) with the validated application form.</dd>
          <dd>
            <blockquote lang="vi">
              <p>Thất bại là mẹ thành công.</p>
            </blockquote>
            <p class="about-quote-en" lang="en">English: <q>Failure is the mother of success.</q> <cite>Vietnamese
                proverb</cite></p>
          </dd>

          <!-- FOURTH MEMBER: QUAN -->
          <dt>Bùi Minh Quân <span class="about-id">106216214</span></dt>
          <dd><strong>Contribution:</strong> Built the About page (SCRUM-16) with the team information.</dd>
          <dd>
            <blockquote lang="vi">
              <p>Có chí thì nên.</p>
            </blockquote>
            <p class="about-quote-en" lang="en">English: <q>Where there is a will, there is a way.</q> <cite>Vietnamese
                proverb</cite></p>
          </dd>
        </dl>
      </section>

      <!-- SECTION 3: GROUP PHOTO -->
      <section aria-labelledby="about-photo">
        <h2 id="about-photo">Our photo</h2>
        <figure class="about-figure">
          <img src="images/group_photo.jpg"
            alt="The four members of team jarviSONy and their lecturer gathered around a computer screen in a classroom"
            width="650" height="480">
          <figcaption>Figure 1: Team jarviSONy: Vinh, Nguyen, Dung and Quan.</figcaption>
        </figure>
      </section>

      <!-- SECTION 4: FUN FACTS TABLE -->
      <section aria-labelledby="about-facts">
        <h2 id="about-facts">Fun facts</h2>
        <div class="table-scroll" role="region" aria-label="Fun facts table (scrollable)" tabindex="0">
          <table class="about-table">
            <caption>Fun facts about each team member</caption>
            <thead>
              <tr>
                <th scope="col">Member</th>
                <th scope="col">Dream job</th>
                <th scope="col">Coding snack</th>
                <th scope="col">Hometown</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">Bùi Thế Vinh</th>
                <td>Data Scientist</td>
                <td>Instant noodles</td>
                <td>Ho Chi Minh City</td>
              </tr>
              <tr>
                <th scope="row">Nguyễn Khoa Nguyên</th>
                <td>UX Designer &amp; Game Developer</td>
                <td>Ice Cream</td>
                <td>Ho Chi Minh City</td>
              </tr>
              <tr>
                <th scope="row">Phạm Trần Quốc Dũng</th>
                <td>Machine Learning Engineer</td>
                <td>Dried mango</td>
                <td>Khanh Hoa</td>
              </tr>
              <tr>
                <th scope="row">Bùi Minh Quân</th>
                <td>Cybersecurity &amp; Game Developer</td>
                <td>Dark chocolate</td>
                <td>Ho Chi Minh City</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  
<?php include __DIR__ . '/footer.inc'; ?>
