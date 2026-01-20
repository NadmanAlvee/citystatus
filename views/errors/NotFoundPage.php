<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Page Not Found — CityStatus</title>
      <style>
          :root {
              --primary: #2563eb;
              --text-main: #1e293b;
              --text-muted: #64748b;
              --bg: #f8fafc;
          }

          body {
              font-family: 'Inter', system-ui, -apple-system, sans-serif;
              background-color: var(--bg);
              color: var(--text-main);
              margin: 0;
              display: flex;
              align-items: center;
              justify-content: center;
              height: 100vh;
              text-align: center;
          }

          .container {
              max-width: 500px;
              padding: 2rem;
          }

          .error-code {
              font-size: 8rem;
              font-weight: 900;
              margin: 0;
              line-height: 1;
              background: linear-gradient(180deg, #2563eb 0%, #dbeafe 100%);
              -webkit-background-clip: text;
              -webkit-text-fill-color: transparent;
              opacity: 0.8;
          }

          h1 {
              font-size: 1.875rem;
              margin: 1rem 0;
              color: var(--text-main);
          }

          p {
              color: var(--text-muted);
              line-height: 1.6;
              margin-bottom: 2rem;
          }

          .actions {
              display: flex;
              gap: 1rem;
              justify-content: center;
              flex-wrap: wrap;
          }

          .btn {
              padding: 0.75rem 1.5rem;
              border-radius: 8px;
              text-decoration: none;
              font-weight: 600;
              font-size: 0.95rem;
              transition: all 0.2s;
          }

          .btn-primary {
              background-color: var(--primary);
              color: white;
              box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
          }

          .btn-primary:hover {
              background-color: #1d4ed8;
              transform: translateY(-1px);
          }

          .btn-outline {
              border: 1px solid #cbd5e1;
              color: var(--text-main);
              background: white;
          }

          .btn-outline:hover {
              background: #f1f5f9;
          }

          .footer {
              margin-top: 3rem;
              font-size: 0.875rem;
              color: var(--text-muted);
          }

          /* Subtle "Status" pulse animation */
          .pulse {
              display: inline-block;
              width: 8px;
              height: 8px;
              background-color: #ef4444;
              border-radius: 50%;
              margin-right: 6px;
              animation: blink 1.5s infinite;
          }

          @keyframes blink {
              0% { opacity: 1; }
              50% { opacity: 0.3; }
              100% { opacity: 1; }
          }
      </style>
  </head>
  <body>

  <div class="container">
      <div class="error-code">404</div>
      <h1>Location Unknown</h1>
      <p>
          The page you are looking for might have been removed, had its name changed, 
          or is temporarily unavailable in the CityStatus network.
      </p>

      <div class="actions">
          <a href="/citystatus/index" class="btn btn-primary">Go to homepage</a>
      </div>

      <div class="footer">
          <span class="pulse"></span> System Status: <span style="font-weight: 600;">Endpoint Disconnected</span>
      </div>
  </div>
</body>
</html>
