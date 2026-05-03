# Campaign Microsite Builder

A multi-tenant campaign landing page builder built with CodeIgniter 4, Twig, and PostgreSQL. Campaign managers create branded microsites with custom forms, countdown timers, and UTM tracking through an admin panel. Public visitors see a branded page, fill out the form, and share on social media. All submissions are tracked with analytics.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | CodeIgniter 4.7 |
| Language | PHP 8.3+ |
| Database | PostgreSQL 16 |
| Templating | Twig 3 (public microsites) + CI4 views (admin) |
| Auth | Google OAuth (whitelist-based, no self-registration) |
| Frontend | Bootstrap 5 (admin), Twig templates (public) |
| Testing | PHPUnit 10 (20 tests, 29 assertions) |

## Features

- Create branded campaign microsites with custom colors, fonts, logos, and hero images
- Dynamic form builder with 8 field types (text, email, phone, textarea, dropdown, checkbox, radio, date)
- Twig-based template rendering for public-facing microsites
- Countdown timer with configurable target date
- Campaign scheduling with start/end dates
- UTM parameter capture (source, medium, campaign, content) on page views and submissions
- Campaign analytics dashboard (page views, submissions, conversion rate)
- CSV export of form submissions
- Social sharing buttons (Facebook, Twitter, LinkedIn)
- Campaign lifecycle: draft, published, closed
- Google OAuth login for admin (no password-based auth)

## Project Structure

```
campaign-microsite/
  app/
    Controllers/
      Admin/              # DashboardController, CampaignController
      Auth/               # LoginController (Google OAuth)
      Public/             # MicrositeController (public campaign pages)
    Database/
      Migrations/         # Users, Campaigns, CampaignFields, Submissions, PageViews
      Seeds/              # AdminSeeder (paulinopjc@gmail.com)
    Filters/              # AdminAuth (session-based auth guard)
    Libraries/            # TwigRenderer (Twig environment setup)
    Models/               # User, Campaign, CampaignField, Submission,
                          # SubmissionValue, PageView
    Views/
      admin/              # Dashboard, campaign list, create/edit, submissions
      auth/               # Google OAuth login page
      layouts/            # Admin layout (navbar, sidebar)
  templates/
    default.twig          # Public microsite template (branding, form, countdown, sharing)
  tests/
    Feature/              # AdminAccessTest, MicrositeTest
    Models/               # CampaignModelTest, SubmissionModelTest
```

## Getting Started

### Prerequisites

- PHP 8.3+ with extensions: intl, pgsql, pdo_pgsql, mbstring, curl
- Composer
- Docker Desktop (for PostgreSQL)
- Google Cloud Console account (for OAuth Client ID)

### Setup

```bash
git clone <repo-url>
cd campaign-microsite

composer install

# Start PostgreSQL
docker run -d --name searix_postgres -p 5433:5432 \
  -e POSTGRES_USER=postgres \
  -e POSTGRES_PASSWORD=postgres \
  -e POSTGRES_DB=searix_microsites \
  postgres:16-alpine

# Copy environment file and configure
cp env .env
```

Edit `.env`:

```
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8082'
database.default.hostname = localhost
database.default.database = searix_microsites
database.default.username = postgres
database.default.password = postgres
database.default.DBDriver = Postgre
database.default.port = 5433
database.default.charset = utf8
GOOGLE_CLIENT_ID = your-google-client-id-here
```

```bash
# Run migrations and seed admin user
php spark migrate
php spark db:seed AdminSeeder

# Create upload directories
mkdir -p public/uploads/logos public/uploads/heroes

# Start the dev server
php -S localhost:8082 -t public
```

Open `http://localhost:8082` in your browser.

### Google OAuth Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/) > APIs & Services > Credentials
2. Create an OAuth Client ID (Web application)
3. Add `http://localhost:8082` to Authorized JavaScript origins
4. Copy the Client ID into `.env` as `GOOGLE_CLIENT_ID`
5. Go to OAuth consent screen > Test users and add your Google email

### Run Tests

```bash
# Create test database (one-time)
docker exec searix_postgres psql -U postgres -c "CREATE DATABASE campaign_microsite_test;"

# Run tests
php vendor/bin/phpunit
```

```
OK (20 tests, 29 assertions)
```

## How It Works

### Admin Flow

1. Admin signs in with Google OAuth
2. Creates a campaign with branding (colors, logo, hero), form fields, and dates
3. Publishes the campaign to make it live at `/c/<slug>`
4. Views submissions and analytics on the dashboard
5. Exports submissions as CSV

### Public Flow

1. Visitor lands on `/c/<slug>` (with optional UTM parameters)
2. Page view is recorded for analytics
3. Visitor fills out the dynamic form
4. Submission is saved with UTM tracking data
5. Visitor sees thank you message and social sharing buttons

### Campaign Branding

Each campaign stores branding as JSON: primary color, background color, text color, font family, logo URL, and hero image URL. The Twig template renders these as CSS custom properties for consistent styling.

### UTM Tracking

Page views and submissions capture `utm_source`, `utm_medium`, `utm_campaign`, and `utm_content` from URL parameters. The dashboard shows conversion rate (submissions / page views) and the CSV export includes UTM data per submission.

## Authentication

Google OAuth with whitelist-based access. No email/password auth, no public registration.

1. Admin user `paulinopjc@gmail.com` is seeded on first deploy
2. Admins add new users directly to the database (name + email + role)
3. Users sign in with Google; backend verifies the ID token and checks the email against the users table
4. Only users in the database can sign in

## License

MIT
