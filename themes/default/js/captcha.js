function newCaptcha()
{
  // Refresh CAPTCHA, random number is added to URL to bypass browser cache
  document.getElementById('captcha-image').src = base_url + '/index.php?action=captcha;time=' + (new Date).getTime();
}