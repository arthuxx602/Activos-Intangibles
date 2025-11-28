# Activos Intangibles - AI Coding Agent Instructions

## Project Overview
**Type**: Laravel 12 full-stack web application with role-based access control  
**Primary Domain**: Intangible assets management with project-based organization  
**Key Stack**: PHP 8.2, Laravel 12, MySQL/SQLite, Vite, Blade templates, Alpine.js

### Architecture Highlights
- **Role-based system** with three tiers: Admin (rol=1), Moderador (rol=2), Inversionista (rol=3)
- **Legacy authentication** (session-based, not Laravel Auth) stored in `usuario2` table
- **Project-centered workflow** - users must select/have a project before accessing role-specific dashboards
- **Hybrid frontend**: Mix of modern Vite + Blade templates and legacy PHP (public/Legacy/)

## Critical Context for Agents

### Database Schema Key Points
- **Usuario model** (`app/Models/Usuario.php`): Custom authentication using `usuario2` table, NOT Laravel's default `users` table
  - Primary key: `ID_Usuario` (not `id`)
  - Password field: `Contraseña` (Spanish spelling, stored as plaintext - security debt)
  - Auth column: `Correo` (uses email as username, not username field)
- **Proyecto model** (`app/Models/Proyecto.php`): Main project entity with `proyecto` table
- **ProyectoUsuario pivot**: Links users to projects via `FK_ID_Usuario` + `FK_ID_Proyecto`
- Role mapping: FK_ID_Rol references role IDs (1=Admin, 2=Moderador, 3=Inversionista)

### Authentication & Authorization Patterns
⚠️ **NOT using Laravel's built-in authentication** - custom legacy system in place:
1. Login endpoint: `POST /login` → validates cedula + plaintext password → stores in session
2. Custom middleware stack:
   - `legacy.auth` → checks `session('authenticated')` instead of Laravel Guard
   - `legacy.role:N` → validates `session('rol')` matches integer N
   - `project.selected` → requires `session('proyecto_seleccionado')` to be set
3. Session keys: `authenticated`, `cedula`, `rol`, `nombre`, `apellido`, `proyecto_seleccionado`, `nombre_proyecto`

### Routing Architecture
- **Public routes**: Landing page (`/`), login (`POST /login`), logout
- **Protected routes by role**: All wrapped in `legacy.auth` middleware, then split by role via `legacy.role:N`
- **Project-dependent routes**: Admin routes DON'T require project selection, but Moderador/Inversionista DO (via `project.selected` middleware)
- **Route grouping convention**: Prefix by role (e.g., `moderador/*`, `inversionista/*`), name-spaced with role (e.g., `moderador.usuarios.index`)
- Controllers organized in subdirectories matching roles: `Controllers/Admin/`, `Controllers/Moderador/`, `Controllers/Inversionista/`

### Frontend Conventions
- **Vite setup**: Single entry point at `resources/js/app.js` + `resources/css/app.css`
- **Blade components**: Stored in `resources/views/components/`
- **CSS strategy**: Per-role stylesheets in `resources/{role}/` directories + global `public/css/{style,media}.css`
- **Legacy code**: Original PHP/HTML in `public/Legacy/` and `public/Admin-php/` - avoid extending these
- **Alpine.js + Tailwind**: Modern stack preferred for new features

### Build & Dev Commands
From `composer.json` scripts:
- `composer dev` - Runs concurrent: `php artisan serve` + queue listener + `npm run dev` (Vite in watch mode)
- `composer test` - Clears config, runs PHPUnit (Feature + Unit tests)
- Single command to boot entire dev environment: **`composer dev`**

### Testing Structure
- PHPUnit configured in `phpunit.xml`
- Test suites: `tests/Unit/` and `tests/Feature/`
- Testing uses in-memory SQLite (`DB_DATABASE=:memory:`)
- Key test env vars: `APP_ENV=testing`, `DB_CONNECTION=sqlite`, `SESSION_DRIVER=array`

## Code Patterns & Conventions

### Model Relationships
```php
// Usuario.php - has many projects via pivot
public function proyectos() {
    return $this->belongsToMany(
        Proyecto::class,
        'proyecto_usuario',
        'FK_ID_Usuario',
        'FK_ID_Proyecto'
    );
}
```
Follow this exact naming: FK_ID_* for foreign keys, ID_* for primary keys.

### Controller Organization
- Place role-specific logic in subdirectory controllers (`Admin/`, `Moderador/`, `Inversionista/`)
- Extend `App\Http\Controllers\Controller` base class
- Use dependency injection for services
- Return redirect with named routes (e.g., `redirect()->route('moderador.usuarios.index')`)

### Validation & Error Handling
- Use `$request->validate()` with custom messages in Spanish
- Blade: Use `@error()` directives to display validation errors
- Return back with `withErrors()` on validation failure

## Development Workflows

### Adding a New Feature
1. **Route**: Add to `routes/web.php` with appropriate middleware (`legacy.auth`, `legacy.role:N`, `project.selected`)
2. **Controller**: Create in `app/Http/Controllers/{Role}/FeatureController.php`
3. **Model** (if needed): Add to `app/Models/`, mirror database naming conventions (FK_ID_*, ID_*)
4. **View**: Create Blade template in `resources/views/{Role}/` directory
5. **Assets**: Import CSS/JS in `resources/js/app.js` or role-specific stylesheets
6. **Test**: Add Feature test to validate workflow, session state, role authorization

### Database Migrations
- New tables should follow naming: `FK_ID_TableName` for foreign keys
- Keep Eloquent models in sync with table names via `protected $table`
- Use `$timestamps = false` only if table lacks created_at/updated_at

### Common Gotchas
- ⚠️ **Password field**: `Contraseña` (Spanish), not `password`. plaintext, not hashed.
- ⚠️ **Auth column**: Uses `Correo`, not `email`. Override via `username()` method in User model.
- ⚠️ **Session-based auth**: NOT compatible with Laravel's standard Auth facade—use `session('key')` directly
- ⚠️ **Project requirement**: Check `project.selected` state before accessing moderador/inversionista dashboards
- ⚠️ **Database backup**: Production schema stored in `database/backups/ActivosIntangibles.sql`

## File Structure Reference
```
app/Models/              # Usuario, Proyecto, ProyectoUsuario
app/Http/Controllers/   # Organized by role subdirectories
app/Http/Middleware/    # legacy.auth, legacy.role, project.selected
routes/web.php          # Main routing, role-based grouping
resources/views/        # Blade templates by role
resources/js|css/       # Vite assets
config/database.php     # DB connection (MySQL default, SQLite for testing)
```

## Integration Points
- **External services**: None currently visible; queue system configured but not actively used
- **Cross-component communication**: Via session variables and redirect/query parameters
- **Database migrations**: Create in `database/migrations/` following Laravel naming (YYYY_MM_DD_HHMMSS_description)
