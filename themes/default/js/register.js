var usernamecache = {};
var registerdata = {};

function checkUsername(element_id)
{
  // Get the username they want ;)
  var handle = document.getElementById(element_id);
  var username = encodeURIComponent(handle.value);
  // Now lets ask your installation
  var results = function(rData)
  {
    if(!usernamecache[username]) usernamecache[username] = rData;
    // So is it allowed? :o
    if(rData == 1)
    {
      handle.style.background = '#D9FFB3';
      registerdata["user"] = true;
    }
    else if(!username)
    {
      handle.style.background = '';
      registerdata["user"] = false;
    }
    else
    {
      handle.style.background = '#FFC6C6';
      registerdata["user"] = false;
    }
  }
  if(!usernamecache[username])
  {
    _.X(base_url + '/index.php?action=interface;sa=user_check', results, 'requested_name=' + username);
  }
  else
  {
    results(usernamecache[username]);
  }
  changeButton();
}

function autoCheckUsername(handle)
{
  handle.style.background = '';
  var usernametext = handle.value;
  if(usernamecache[encodeURIComponent(handle.value)])
    return checkUsername('regUsername');
  window.usernametimer = window.usernametimer?window.usernametimer:[];
  window.usernametimer.push(setTimeout(function()
  {
    window.usernametimer = [];
    if(usernametext == handle.value)
    {
      checkUsername('regUsername')
    }
  }, 500));
}

function check_passwords(handle)
{
  var password = handle.passwrd.value;
  var vpassword = handle.vPasswrd.value;

  if((!password && !vpassword) || (password != vpassword && password.substr(0, vpassword.length) == vpassword) || password.length < 4 || !vpassword)
  {
    handle.passwrd.style.background = '';
    handle.vPasswrd.style.background = '';
    registerdata["password"] = false;
  }
  else if(vpassword && password != vpassword)
  {
    // Red
    handle.passwrd.style.background = '#FFC6C6';
    handle.vPasswrd.style.background = '#FFC6C6';
    registerdata["password"] = false;
  }
  else
  {
    // Green
    handle.passwrd.style.background = '#D9FFB3';
    handle.vPasswrd.style.background = '#D9FFB3';
    registerdata["password"] = true;
  }
  
  changeButton();
  
  // Password strength
  var strength = 0;
  
  if(password.length >= password_recommended)
    strength += 1;
  
  if(password.search(/[a-z]/) != -1)
    strength += 1;
  
  if(password.search(/[A-Z]/) != -1)
    strength += 1;
  
  if(password.search(/[0-9]/) != -1)
    strength += 1;
  
  if(password.search(/[^a-zA-Z0-9]/) != -1)
    strength += 1;
  
  if(password && password.length < password_minimum)
    strength = -1;
  
  switch(strength)
  {
    case 1:
      _.G('password_strength_text').innerHTML = 'Weak';
      _.G('password_strength_text').setAttribute('class', 'password_strength_weak_text');
      _.G('password_strength_bar').innerHTML = '<div class="password_strength_weak_bar"></div>';
      _.G('password_strength_bar').style.display = 'block';
      break;
    case 2:
      _.G('password_strength_text').innerHTML = 'Medium';
      _.G('password_strength_text').setAttribute('class', 'password_strength_medium_text');
      _.G('password_strength_bar').innerHTML = '<div class="password_strength_medium_bar"></div>';
      _.G('password_strength_bar').style.display = 'block';
      break;
    case 3:
      _.G('password_strength_text').innerHTML = 'Strong';
      _.G('password_strength_text').setAttribute('class', 'password_strength_strong_text');
      _.G('password_strength_bar').innerHTML = '<div class="password_strength_strong_bar"></div>';
      _.G('password_strength_bar').style.display = 'block';
      break;
    case 4: case 5:
      _.G('password_strength_text').innerHTML = 'Perfect';
      _.G('password_strength_text').setAttribute('class', 'password_strength_perfect_text');
      _.G('password_strength_bar').innerHTML = '<div class="password_strength_perfect_bar"></div>';
      _.G('password_strength_bar').style.display = 'block';
      break;
    case -1:
      _.G('password_strength_text').innerHTML = 'Too Short';
      _.G('password_strength_text').setAttribute('class', 'password_strength_plain_text');
      _.G('password_strength_bar').innerHTML = '';
      _.G('password_strength_bar').style.display = 'block';
      break;
    default:
      _.G('password_strength_text').innerHTML = '&nbsp;';
      _.G('password_strength_text').setAttribute('class', 'password_strength_plain_text');
      _.G('password_strength_bar').innerHTML = '';
      _.G('password_strength_bar').style.display = 'none';
  }
}

function checkEmail(handle)
{
  email = handle.value;
  if(/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9])+(\.[a-zA-Z0-9_-]+)+$/.test(email)) {
    handle.style.background = '#D9FFB3';
    registerdata["email"] = true;
  }
  else if(!email)
  {
    handle.style.background = '';
    registerdata["email"] = false;
  }
  else
  {
    handle.style.background = '#FFC6C6';
    registerdata["email"] = false;
  }
  changeButton();
}

function agreementCheck()
{
  changeButton();
}

function changeButton()
{
  // Is it checked..? :P
  if(_.G("accepted_agreement").checked && registerdata["email"] && registerdata["password"] && registerdata["user"])
    _.G("submit_registration").disabled = false;
  else
    _.G("submit_registration").disabled = true;
}

setInterval(function()
{
  var form = _.G("registerform");
  autoCheckUsername(_.G('regUsername'));
  //craap! my hands are freeezzzingg!
  checkEmail(form.email);
  check_passwords(form);
  agreementCheck();
}, 1500);