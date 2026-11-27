<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Support\Installer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/** First-time installation wizard. 404s once the site is installed (see InstallationGuard). */
class InstallController extends Controller
{
    public function __construct(private readonly Installer $installer) {}

    public function index(): View
    {
        return view('install.index', [
            'requirements' => $this->installer->requirements(),
            'ready' => $this->installer->requirementsMet(),
            'defaults' => [], // deliberately empty: a new installation starts from a blank form
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->installer->requirementsMet(), 422, 'Server requirements are not met.');

        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:150'],
            'owner_name' => ['required', 'string', 'max:100'],
            'owner_designation' => ['nullable', 'string', 'max:60'],
            'owner_email' => ['required', 'email:rfc', 'max:150'],
            'owner_phone' => ['nullable', 'string', 'max:30'],
            'office_address' => ['nullable', 'string', 'max:300'],
            'office_phone' => ['nullable', 'string', 'max:30'],
            'office_email' => ['required', 'email:rfc', 'max:150'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'home_theme' => ['required', 'in:chooser,aurora,noir,glass'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'admin_name' => ['required', 'string', 'max:100'],
            'admin_email' => ['required', 'email:rfc', 'max:150'],
            'admin_password' => ['required', 'confirmed', Password::min(10)->letters()->mixedCase()->numbers()],
        ]);

        try {
            $this->installer->install($data);
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput($request->except('admin_password', 'admin_password_confirmation'))
                ->withErrors(['install' => 'Installation failed: '.$e->getMessage()]);
        }

        // This request ran on the file-session fallback, so flash data would be lost. Instead the installer left
        // a one-time ticket in the database; the website shows the "installation complete" popup once for it.
        return redirect()->route('home', ['welcome' => $this->installer->welcomeTicket()]);
    }
}
