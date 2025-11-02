import './styles/globals.css';

export const metadata = {
  title: 'Mon App React + Next.js',
};

export default function RootLayout({ children }) {
  return (
    <html lang="fr">
      <body>
        {children}
      </body>
    </html>
  );
}
