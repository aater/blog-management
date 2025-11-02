"use client";

import Link from 'next/link';
import styles from '../styles/Navbar.module.css';
import { useRouter } from "next/navigation";
import { removeToken } from "../lib/auth";

export default function Navbar() {
    const router = useRouter();

    const handleLogout = () => {
      removeToken();
      router.push("/login");
    };

    return (
      <nav className={styles.nav}>
        <div className={styles.logo}>MyBlog</div>
        <div className={styles.links}>
          <Link href="/account" className={styles.link}>
            Mon compte
          </Link>
          <Link href="/articles" className={styles.link}>
            Articles
          </Link>
          <button onClick={handleLogout} style={{
            backgroundColor: "#ff4d4d",
            color: "#fff",
            border: "none",
            padding: "8px 12px",
            cursor: "pointer",
            borderRadius: "4px",
          }}>Déconnexion</button>
        </div>
      </nav>
   );
}