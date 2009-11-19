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

function checkPasswords(handle)
{
  var password = handle.passwrd.value;
  var vPassword = handle.vPasswrd.value;

  if(!password && !vPassword)
  {
    handle.passwrd.style.background = '';
    handle.vPasswrd.style.background = '';
    registerdata["password"] = false;
  }
  else if(password != vPassword || password.length < 4)
  {
    handle.passwrd.style.background = '#FFC6C6';
    handle.vPasswrd.style.background = '#FFC6C6';
    registerdata["password"] = false;
  }
  else
  {
    handle.passwrd.style.background = '#D9FFB3';
    handle.vPasswrd.style.background = '#D9FFB3';
    registerdata["password"] = true;
  }
  changeButton();
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
  checkPasswords(form);
  agreementCheck();
}, 1500);