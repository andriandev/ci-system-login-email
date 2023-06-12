<nav class="navbar navbar-expand-lg bg-dark border-bottom border-bottom-dark" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand" href="/">AndrianDev</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <ul class="navbar-nav me-auto">
                <li>
                    <a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="/">Home</a>
                </li>
                <?php if (session()->get('is_login')) : ?>
                    <li>
                        <a class="nav-link <?= url_is('/profile') ? 'active' : '' ?>" href="/profile">Profile</a>
                    </li>
                <?php else : ?>
                    <li>
                        <a class="nav-link <?= url_is('/register') ? 'active' : '' ?>" href="/register">Register</a>
                    </li>
                    <li>
                        <a class="nav-link <?= url_is('/login') ? 'active' : '' ?>" href="/login">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav me-2">
                <?php if (session()->get('is_login')) : ?>
                    <li>
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>