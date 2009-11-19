function onload()
{
  _.on(_.G('captcha_strength'), 'change', captchaStrengthChange);
  _.on(_.G('captcha_chars'), 'change', captchaStrengthChange);
  _.on(_.G('captcha_chars'), 'keydown', captchaStrengthChange);
  _.on(_.G('captcha_chars'), 'keyup', captchaStrengthChange);
}

function captchaStrengthChange() {
  if(_.G('captcha_strength').options[_.G('captcha_strength').selectedIndex].value)
  {
    _.G('captcha_preview').src = base_url + '/index.php?action=captcha;strength=' + _.G('captcha_strength').options[_.G('captcha_strength').selectedIndex].value + ';chars=' + _.G('captcha_chars').value + ';width=200;height=96;fontsize=28';
    _.G('captcha_preview').style.visibility = 'visible';
  }
  else
  {
    _.G('captcha_preview').style.visibility = 'hidden';
  }
}