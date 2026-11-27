{{--
    One-time "installation complete" popup. $installWelcome is true only on the very first visit after the wizard
    (HomeController::index consumes the ticket, so a refresh or any later visit never shows it).
    Plain <dialog> + inline CSS/JS (nonce), so it works without any build step.
--}}
@if (! empty($installWelcome))
    <dialog id="installWelcome" aria-labelledby="installWelcomeTitle">
        <div class="iw-icon" aria-hidden="true">&#10003;</div>
        <h2 id="installWelcomeTitle">Installation complete!</h2>
        <p>Your website is live. Sign in to the admin panel to change the text, photos, contact details and design — nothing you edit goes public until you press <strong>Publish</strong>.</p>
        <a class="iw-primary" href="{{ route('admin.login') }}">Open the admin panel &rarr;</a>
        <button type="button" class="iw-secondary" data-close>Continue to the website</button>
        <small>Bookmark <code>{{ route('admin.login') }}</code> — this message will not appear again.</small>
    </dialog>
    <style>
        #installWelcome { border: 0; border-radius: 22px; padding: 2rem 2rem 1.5rem; width: min(92vw, 440px); text-align: center; color: #12282a; font: 16px/1.5 "Segoe UI", system-ui, sans-serif; box-shadow: 0 30px 80px -20px rgba(0, 0, 0, .55); }
        #installWelcome::backdrop { background: rgba(4, 24, 26, .62); backdrop-filter: blur(3px); }
        #installWelcome .iw-icon { width: 64px; height: 64px; margin: 0 auto 1rem; border-radius: 50%; display: grid; place-items: center; font-size: 30px; color: #fff; background: linear-gradient(135deg, #12868a, #23cdd2); box-shadow: 0 12px 28px -10px rgba(35, 205, 210, .9); }
        #installWelcome h2 { margin: 0 0 .5rem; font-size: 1.45rem; }
        #installWelcome p { margin: 0 0 1.3rem; color: #55696c; font-size: .95rem; }
        #installWelcome .iw-primary { display: block; padding: .8rem 1rem; border-radius: 12px; background: linear-gradient(135deg, #12868a, #0a5a5c); color: #fff; text-decoration: none; font-weight: 650; box-shadow: 0 8px 20px -8px rgba(15, 109, 111, .7); }
        #installWelcome .iw-secondary { display: block; width: 100%; margin-top: .6rem; padding: .7rem 1rem; border-radius: 12px; border: 1px solid #dbe5e7; background: #fff; color: #12282a; font: inherit; font-weight: 550; cursor: pointer; }
        #installWelcome small { display: block; margin-top: 1rem; color: #7b8e91; font-size: .78rem; }
        #installWelcome code { font-size: .78rem; word-break: break-all; }
    </style>
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        (function () {
            var dialog = document.getElementById('installWelcome');
            if (!dialog || typeof dialog.showModal !== 'function') { return; }
            dialog.showModal();
            dialog.querySelector('[data-close]').addEventListener('click', function () { dialog.close(); });
            // Drop ?welcome=… from the address bar so a copied or reloaded URL stays clean.
            if (window.history && history.replaceState) { history.replaceState(null, '', location.pathname); }
        })();
    </script>
@endif
