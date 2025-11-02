import '../styles/globals.css';
import { useEffect } from 'react';
import { getToken } from '../lib/auth';
import { setAuthToken } from '../lib/api';

function MyApp({ Component, pageProps }) {
  useEffect(() => {
    const token = getToken();
    if (token) setAuthToken(token);
  }, []);

  return <Component {...pageProps} />;
}

export default MyApp;