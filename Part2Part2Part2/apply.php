<?php
// apply.php | owner: Dung | Part 2 page template (see guide Part A6)
// Part 2 changes to the Part 1 form:
//  - posts to process_eoi.php with novalidate: ALL checks happen on the server (brief, decision D09)
//  - required / pattern / minlength / maxlength / title removed; aria-required="true" keeps the
//    "this field is required" information for screen readers; the visible * stays
//  - job reference pre-filled from apply.php?job=LL101 (links on the Jobs page)
$page_title       = "Apply";
$body_class       = "page-apply";
$active_page      = "apply";
$meta_description = 'Apply for a job at Pedapigogy Learn using our online application form.';
$extra_head = <<<'HTML'
<style>
    .page-apply .apply-hint code {
      font-family: var(--font-mono);
    }
  </style>
HTML;
require_once __DIR__ . '/functions.php';

// Pre-fill: accept ?job= only when it is exactly 5 letters or digits; anything else is ignored.
// The value is printed through e(), never raw.
$prefill_job = '';
if (isset($_GET['job']) && is_string($_GET['job']) && preg_match('/^[A-Za-z0-9]{5}$/', $_GET['job']) === 1) {
    $prefill_job = strtoupper($_GET['job']);
}

include __DIR__ . '/header.inc';
?>
    <div class="container">
      <h1>Apply now</h1>
      <p class="apply-intro">Fill in every field marked with <span aria-hidden="true">*</span><span
          class="visually-hidden">an asterisk</span>. Use the reference number from the Jobs page:
        <strong>LL101</strong>, <strong>LL102</strong>, <strong>LL103</strong> or <strong>LL104</strong>. We use your details only to process your application.
      </p>

      <!-- FORM: POST to process_eoi.php. novalidate = the browser does not check anything; the server does (D09). -->
      <form class="apply-form" action="process_eoi.php" method="post" novalidate>

        <!-- GROUP 1: which job the applicant wants -->
        <fieldset class="apply-fieldset">
          <legend>The job</legend>
          <div class="apply-grid">
            <div class="apply-field">
              <label class="apply-label apply-label--required" for="job-ref">Job reference number</label>
              <input type="text" id="job-ref" name="job_ref" value="<?php echo e($prefill_job); ?>"
                placeholder="LL101" autocomplete="off" aria-required="true" aria-describedby="job-ref-hint">
              <p class="apply-hint" id="job-ref-hint">Exactly 5 letters or digits, for example <code>LL101</code>.</p>
            </div>
          </div>
        </fieldset>

        <!-- GROUP 2: personal details. Gender is a nested fieldset because the radios answer one question. -->
        <fieldset class="apply-fieldset">
          <legend>About you</legend>
          <div class="apply-grid">
            <div class="apply-field">
              <label class="apply-label apply-label--required" for="first-name">First name</label>
              <input type="text" id="first-name" name="first_name" autocomplete="given-name" placeholder="Dung"
                aria-required="true" aria-describedby="first-name-hint">
              <p class="apply-hint" id="first-name-hint">Letters only, up to 20 (no accents or spaces).</p>
            </div>
            <div class="apply-field">
              <label class="apply-label apply-label--required" for="last-name">Last name</label>
              <input type="text" id="last-name" name="last_name" autocomplete="family-name" placeholder="Pham"
                aria-required="true" aria-describedby="last-name-hint">
              <p class="apply-hint" id="last-name-hint">Letters only, up to 20 (no accents or spaces).</p>
            </div>
            <!-- DOB: type="text", not type="date", so the value is always dd/mm/yyyy; process_eoi.php converts it. -->
            <div class="apply-field apply-field--wide">
              <label class="apply-label apply-label--required" for="dob">Date of birth</label>
              <input type="text" id="dob" name="dob" autocomplete="bday" placeholder="dd/mm/yyyy"
                inputmode="numeric" aria-required="true" aria-describedby="dob-hint">
              <p class="apply-hint" id="dob-hint">Format: <code>dd/mm/yyyy</code>, for example 25/12/2000.</p>
            </div>
            <fieldset class="apply-fieldset apply-field--wide">
              <legend class="apply-label apply-label--required">Gender</legend>
              <div class="apply-choices">
                <label class="apply-choice"><input type="radio" name="gender" value="female"> Female</label>
                <label class="apply-choice"><input type="radio" name="gender" value="male"> Male</label>
                <label class="apply-choice"><input type="radio" name="gender" value="non-binary"> Non-binary</label>
                <label class="apply-choice"><input type="radio" name="gender" value="not-stated"> Prefer not to
                  say</label>
              </div>
            </fieldset>
          </div>
        </fieldset>

        <!-- GROUP 3: address -->
        <fieldset class="apply-fieldset">
          <legend>Your address</legend>
          <div class="apply-grid">
            <div class="apply-field apply-field--wide">
              <label class="apply-label apply-label--required" for="street">Street address</label>
              <input type="text" id="street" name="street" autocomplete="address-line1" placeholder="12 Burwood Road"
                aria-required="true" aria-describedby="street-hint">
              <p class="apply-hint" id="street-hint">Up to 40 characters.</p>
            </div>
            <div class="apply-field">
              <label class="apply-label apply-label--required" for="suburb">Suburb or town</label>
              <input type="text" id="suburb" name="suburb" autocomplete="address-level2" placeholder="Hawthorn"
                aria-required="true" aria-describedby="suburb-hint">
              <p class="apply-hint" id="suburb-hint">Up to 40 characters.</p>
            </div>
            <!-- STATE: the empty first option means "nothing chosen"; the server rejects it. -->
            <div class="apply-field">
              <label class="apply-label apply-label--required" for="state">State</label>
              <select id="state" name="state" autocomplete="address-level1" aria-required="true">
                <option value="" selected>Select a state</option>
                <option value="VIC">VIC</option>
                <option value="NSW">NSW</option>
                <option value="QLD">QLD</option>
                <option value="NT">NT</option>
                <option value="WA">WA</option>
                <option value="SA">SA</option>
                <option value="TAS">TAS</option>
                <option value="ACT">ACT</option>
              </select>
            </div>
            <!-- INLINE CSS EXAMPLE (contract A10): a postcode is only 4 digits, so the box is narrower. -->
            <div class="apply-field">
              <label class="apply-label apply-label--required" for="postcode">Postcode</label>
              <input type="text" id="postcode" name="postcode" inputmode="numeric" autocomplete="postal-code"
                placeholder="3122" style="max-width: 8rem;" aria-required="true" aria-describedby="postcode-hint">
              <p class="apply-hint" id="postcode-hint">Exactly 4 digits that match your state.</p>
            </div>
          </div>
        </fieldset>

        <!-- GROUP 4: contact details -->
        <fieldset class="apply-fieldset">
          <legend>How we reach you</legend>
          <div class="apply-grid">
            <div class="apply-field">
              <label class="apply-label apply-label--required" for="email">Email</label>
              <input type="email" id="email" name="email" autocomplete="email" placeholder="name@example.com"
                aria-required="true" aria-describedby="email-hint">
              <p class="apply-hint" id="email-hint">For example <code>name@example.com</code>.</p>
            </div>
            <div class="apply-field">
              <label class="apply-label apply-label--required" for="phone">Phone number</label>
              <input type="tel" id="phone" name="phone" inputmode="numeric" autocomplete="tel" placeholder="0412345678"
                aria-required="true" aria-describedby="phone-hint">
              <p class="apply-hint" id="phone-hint">8 to 12 digits, no spaces or symbols.</p>
            </div>
          </div>
        </fieldset>

        <!-- GROUP 5: skills. At least one must be ticked (checked on the server, rule L).
             Labels match jobs.php exactly (contract A9.2). -->
        <fieldset class="apply-fieldset">
          <legend>Your skills</legend>
          <fieldset class="apply-fieldset" aria-describedby="skills-hint">
            <legend class="apply-label apply-label--required">Tick every skill you have</legend>
            <p class="apply-hint" id="skills-hint">Tick at least one.</p>
            <div class="apply-choices">
              <label class="apply-choice"><input type="checkbox" name="skills[]" value="html"><span>HTML5 and semantic
                  markup</span></label>
              <label class="apply-choice"><input type="checkbox" name="skills[]" value="css"><span>CSS3 layout (Flexbox
                  and Grid)</span></label>
              <label class="apply-choice"><input type="checkbox" name="skills[]"
                  value="accessibility"><span>Accessibility (WCAG 2.2)</span></label>
              <label class="apply-choice"><input type="checkbox" name="skills[]" value="responsive"><span>Responsive
                  design</span></label>
              <label class="apply-choice"><input type="checkbox" name="skills[]" value="git"><span>Git and
                  GitHub</span></label>
              <label class="apply-choice"><input type="checkbox" name="skills[]" value="wireframing"><span>Wireframing
                  and prototyping</span></label>
              <label class="apply-choice"><input type="checkbox" name="skills[]" value="research"><span>User research
                  and usability testing</span></label>
              <label class="apply-choice"><input type="checkbox" name="skills[]" value="writing"><span>Clear written
                  communication</span></label>
            </div>
          </fieldset>
          <!-- The only optional field in the form (the brief: all fields except the textarea are required). -->
          <div class="apply-field">
            <label class="apply-label" for="other-skills">Other skills (optional)</label>
            <textarea id="other-skills" name="other_skills" rows="5" aria-describedby="other-skills-hint"></textarea>
            <p class="apply-hint" id="other-skills-hint">Up to 1000 characters.</p>
          </div>
        </fieldset>

        <div class="apply-actions">
          <button class="btn" type="submit">Submit application</button>
          <button class="btn" type="reset">Clear form</button>
        </div>
      </form>
    </div>

<?php include __DIR__ . '/footer.inc'; ?>
