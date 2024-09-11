<nav class="navbar">
    <div class="container">
        <div class="logo-container">
          <a href="/">
          <img class="icon logo" src="/assets/img/Nexaframe.png" alt="Brand Logo">
          </a>
        </div>
        <ul class="navbar-links">
            <li>
                <a href="/home">NexaFrame</a>
            </li>
        </ul>
        <a href="/installer" class="Button Primary">Commencer</a>
        <div class="theme-switcher" id="themeSwitcher">
        <img id="icon-sun" class="icon icon-sun" src="/assets/svg/icon/sun.svg" alt="Sun Icon"/>
        <img id="icon-moon" class="icon icon-moon" src="/assets/svg/icon/moon.svg" alt="Moon Icon"/>
        </div>
    </div>
    <div class="line"></div>

</nav>
<script>
    document.addEventListener('DOMContentLoaded', () => {
  const moreButton = document.querySelector('.navbar-links .more');
  moreButton.addEventListener('click', function() {
    this.querySelector('.dropdown-content').classList.toggle('show');
  });
});
    document.addEventListener('DOMContentLoaded', () => {
  const themeSwitcher = document.getElementById('themeSwitcher');
  const iconSun = document.getElementById('icon-sun');
  const iconMoon = document.getElementById('icon-moon');

  themeSwitcher.addEventListener('click', () => {
    const isDarkMode = document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
    updateIcons(isDarkMode);
  });

  function updateIcons(isDarkMode) {
    if (isDarkMode) {
      iconSun.style.display = 'block';
      iconMoon.style.display = 'none';
    } else {
      iconSun.style.display = 'none';
      iconMoon.style.display = 'block';
    }
  }

  // Initialiser l'icône en fonction du mode sombre stocké
  const isDarkModeInitial = localStorage.getItem('darkMode') === 'enabled';
  document.body.classList.toggle('dark-mode', isDarkModeInitial);
  updateIcons(isDarkModeInitial);
});
</script>