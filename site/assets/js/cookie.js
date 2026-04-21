document.addEventListener('DOMContentLoaded', function () {
  const banner = document.getElementById('cookieBanner');
  const acceptBtn = document.getElementById('acceptCookies');
  const cookieName = 'cookie_consent_accepted';

  function hasConsent() {
    return document.cookie.split(';').some(cookie => cookie.trim().startsWith(cookieName + '=true'));
  }

  function setConsent() {
    const expiry = new Date();
    expiry.setFullYear(expiry.getFullYear() + 1);
    document.cookie = `${cookieName}=true; path=/; expires=${expiry.toUTCString()}`;
  }

  if (!hasConsent()) {
    setTimeout(() => {
      banner.classList.remove('d-none');
    }, 10000);
  }

  if (acceptBtn) {
    acceptBtn.addEventListener('click', function () {
      setConsent();
      banner.classList.add('d-none');
    });
  }
});
