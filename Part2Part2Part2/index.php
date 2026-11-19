<?php
// index.php | owner: Vinh | Part 2 page template (see guide Part A6)
$page_title       = "Home";
$body_class       = "page-home";
$active_page      = "home";
$meta_description = 'Pedapigogy Learn builds digital learning tools and online platforms for schools, universities and workplaces.';
$extra_head = <<<'HTML'
<style>
        /* Embedded example: slightly larger slogan on Home for emphasis. */
        .page-home .home-hero__slogan {
            font-size: var(--text-lg);
        }
    </style>
HTML;
require_once __DIR__ . '/functions.php';
include __DIR__ . '/header.inc';
?>
        <!-- Hero section: contains company name (h1), slogan and hero illustration. Semantic use: h1 is page title, sections group related content. -->
        <section class="home-hero home-section" aria-label="Welcome">
            <div class="container home-hero__inner">
                <div>
                    <!-- h1 must be unique on the page. Use company name for the main heading. -->
                    <h1>Pedapigogy Learn</h1>
                    <p class="home-hero__slogan">Learning that leaves no one behind.</p>
                    <p class="home-hero__desc">Pedapigogy Learn builds digital learning tools and online platforms for
                        schools, universities and workplaces. Every product is designed to be accessible and inclusive
                        from the first sketch.</p>

                    <!-- Search form: role=search, GET to search.php (Part 2: the search runs on the jobs table in the database). -->
                    <form class="home-search" role="search" action="search.php" method="get">
                        <label for="q" class="visually-hidden">Search jobs</label>
                        <input id="q" name="q" type="search" placeholder="Search jobs by keyword, e.g. 'accessibility'"
>
                        <button class="btn" type="submit">Search</button>
                        <p class="home-search__note">Searches the open jobs in our database.</p>
                    </form>
                </div>

                <figure class="home-hero__image">
                    <img src="images/hero-learning.svg"
                        alt="Illustration of a laptop showing a lesson, with a small pink student pig peeking from behind the screen and a book floating beside it" width="600"
                        height="400">
                </figure>
            </div>
        </section>

        <!-- Hiring timeline: demonstrates merged cells (colspan and rowspan) as required. Wrapped in .table-scroll for small screens. -->
        <section class="home-section container">
            <h2>Hiring timeline</h2>
            <div class="table-scroll">
                <table class="data-table home-table">
                    <caption>Planned hiring and milestones (fictional)</caption>
                    <thead>
                        <tr>
                            <th scope="col">Month</th>
                            <th scope="col">Role</th>
                            <th scope="col">Key dates</th>
                            <th scope="col">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" rowspan="2">Nov 2026</th>
                            <td>LL101 Front-End Developer</td>
                            <td>Applications open 1 Nov</td>
                            <td>Shortlisted by 15 Nov</td>
                        </tr>
                        <tr>
                            <td colspan="2">LL102 UX and Accessibility Designer</td>
                            <td>Interviews held late Nov</td>
                        </tr>
                        <tr>
                            <th scope="row">Dec 2026</th>
                            <td>Offer and onboarding</td>
                            <td>Offers sent 5 Dec</td>
                            <td>Start dates flexible</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Aboriginal & Torres Strait Islander commitment section: owned by Vinh and present on index and footer. Use semantic <aside> to mark an important related statement. -->
        <section class="home-section home-commitment container" aria-labelledby="commitment-title">
            <h2 id="commitment-title" class="home-commitment__title">Our commitment to reconciliation</h2>
            <p>We acknowledge and respect the Traditional Owners of the lands where we live and work, and we pay our
                respects to Elders past, present and emerging.</p>
        </section>

    
<?php include __DIR__ . '/footer.inc'; ?>
