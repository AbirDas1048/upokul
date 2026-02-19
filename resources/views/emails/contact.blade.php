<h2>New Contact Message</h2>

<p><strong>Name:</strong> {{ $contactMail->name }}</p>
<p><strong>Email:</strong> {{ $contactMail->email }}</p>
<p><strong>Phone:</strong> {{ $contactMail->phone ?? 'N/A' }}</p>
<p><strong>Subject:</strong> {{ $contactMail->subject }}</p>

<hr>

<p>{{ $contactMail->mail_message }}</p>
