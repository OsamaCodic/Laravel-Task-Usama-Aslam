<html>
	<head>
		<style>
		</style>
	</head>
	<body style="padding: 0 10%;font: normal 12px/150% Arial, Helvetica, sans-serif;">
		
		<p>Hi {{$data['first_name']}},</p>
		<p>We just need to verify your email address before you can access [companyName portal].</p>
		<p>Verify your email address [verification link]</p>
		
		Email: {{$data['Email']}}
		Thanks! – The [companyName] team
	</body>
</html>
