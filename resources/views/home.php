@merge(layouts.main)
{{ @title(Welcome to Kikopolis) }}

<div class="hero" id="hero">
    <div class="hero__content hero__content-left">
        <h3 class="hero__title hero__title-left">
            Who Am <span class="emphasis__text">I</span>?
        </h3>
        <p class="hero__paragraph-left">
            A Full Stack Web Developer with a focus on back-end technologies.
        </p>
        <p class="hero__paragraph-left">
            See <a href="#skills">skills</a> for more information!
        </p>
        <h3 class="hero__title hero__title-left">
            What I <span class="emphasis__text">Do</span>?
        </h3>
        <p class="hero__paragraph-left">
            I create web apps!
        </p>
        <p class="hero__paragraph-left">
            Also native apps! (probably, someday, when I get around to it)
        </p>
        <h3 class="hero__title hero__title-left">
            Short Description of This <span class="emphasis__text">Site</span>
        </h3>
        <p class="hero__paragraph-left">
            This website is my personal portfolio and blog platform.
        </p>
        <p class="hero__paragraph-left">
            This website uses a custom PHP framework. Why?
        </p>
        <p class="hero__paragraph-left">
            Because all developers want to develop their own framework.
        </p>
        <h3 class="hero__title hero__title-left pt-5 emphasis__text">
            Have a delicious cup of CSS coffee!
        </h3>
    </div>
    <div class="hero__content hero__content-right">
        <div class="cup__container">
            <div class="cup">
                <div class="cup__top"></div>
                <div class="cup__top-inside">
                    <div class="cup__top-coffee"></div>
                </div>
                <div class="vapour__mask">
                    <div class="vapours">
                        <div class="vapour__streak">
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                        </div>
                        <div class="vapour__streak">
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                        </div>
                        <div class="vapour__streak">
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                        </div>
                        <div class="vapour__streak">
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                        </div>
                        <div class="vapour__streak">
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                        </div>
                        <div class="vapour__streak">
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                            <div class="vapour__bubble"></div>
                        </div>
                    </div>
                </div>
                <div class="cup__handle"></div>
            </div>
        </div>
    </div>
</div>
<div class="skills" id="skills">
    <h2 class="section__title">
        What <span class="emphasis__text">Skills</span> Do I Have?
    </h2>
    <div class="section__content pb-5 mb-5">
        <h3>
            Keywords - PHP, Java, Javascript, Typescript, MySQL, Symfony, Laravel, React, Vue, Spring Boot.
        </h3>
    </div>
    <div class="section__content w-50">
        <ul>
            <li class="section__content-paragraph">
                <span class="emphasis__text">PHP</span> - <span>symfony, Magento 2, Laravel, Doctrine, PHPUnit, Codeception, Behat</span>
            </li>
            <li class="section__content-paragraph">
                <span class="emphasis__text">Java</span> - <span>Spring Boot, JUnit and Hibernate</span>
            </li>
            <li class="section__content-paragraph">
                <span class="emphasis__text">JavaScript</span> - <span>regular old JavaScript, JQuery, Vue, React and TypeScript!</span>
            </li>
            <li class="section__content-paragraph">
                <span class="emphasis__text">CSS/SCSS</span> - <span>Bootstrap, Tailwind and Material</span>
            </li>
            <li class="section__content-paragraph">
                <span class="emphasis__text">Managing Servers</span> - <span>Docker, Ubuntu, Linode Cloud</span>
            </li>
            <li class="section__content-paragraph">
                <span class="emphasis__text">C++, Go and Python</span> - <span>Interested, minor projects but nothing noteworthy.</span>
            </li>
        </ul>
    </div>
</div>
<div class="portfolio" id="portfolio">
    <h2 class="section__title">My <span class="emphasis__text">Portfolio</span></h2>
    <div class="section__content">
        <div class="portfolio__card">
            <h4>Ignis</h4>
            <p>A Pet Clinic app in Spring Boot. Junit, Hibernate and Thymeleaf.</p>
            <a href="https://github.com/kikopolis/pet-clinic">Pet Clinic Github</a>
        </div>
        <div class="portfolio__card">
            <h4>Ignis</h4>
            <p>A project management app. Very simplistic and spartan frontend.</p>
            <a href="https://github.com/kikopolis/ignis">Ignis Github</a>
        </div>
        <div class="portfolio__card">
            <h4>Vehicle Management</h4>
            <p>The most comprehensive app in symfony. Near full test coverage, full translations of English and Estonian, and a strictly typed, tested backend.
                Ambitious and a WIP.</p>
            <a href="https://github.com/kikopolis/vehicle_management">Vehicle Management GitHub</a>
        </div>
        <div class="portfolio__card">
            <h4>Collection of Projects</h4>
            <p>Collection of small HTML, CSS and JS projects. Also, some JS games.</p>
            <a href="/games">Javascript Games</a>
            <a href="https://github.com/kikopolis/random_web_projects">Random Web Projects</a>
        </div>
        <div class="portfolio__card">
            <h4>MiNiPoSt App</h4>
            <p>A twitter like small posts app. Two different unfinished versions. These projects taught me the necessity of tests.</p>
            <a href="https://github.com/kikopolis/micro_posts">MiniPost GitHub 1</a>
            <a href="https://github.com/kikopolis/micro_posts_v2">MiniPost GitHub 2</a>
        </div>

    </div>
</div>
<div class="contact" id="contact">
    <h2 class="section__title"><span class="emphasis__text">Contact</span> and <span class="emphasis__text">About</span> me</h2>
    <div class="section__content">
        <p>Write an email to <span class="emphasis__text">kristo.leas@gmail.com</span></p>
    </div>
</div>
