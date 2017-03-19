Hello {{$name}},

<br/><br/>This the password verify email for your account at <a href="sltravelmate.com">sltravelmate.com</a>

<br/>Please visit the following link.

<a href="http://localhost:8000/api/auth/verifyemail?token={{$token}}&this_email={{$email}}">
<br/>http://localhost:8000/api/auth/verifyemail?token={{$token}}&this_email={{$email}}
</a>

<br/><br/>Thank you.

<br/><br/>SLTravelmate Team.
<br/>{{\Carbon\Carbon::now()}}