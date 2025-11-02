'use client'

import { useState } from 'react';
import Navbar from '../components/Navbar';
import styles from '../styles/Account.module.css';
import { useRouter } from 'next/navigation';
import { useEffect } from 'react';
import api from "../lib/api";

export default function AccountPage() {
  const [user, setUser] = useState({ name: "", email: "" });
  const [message, setMessage] = useState("");

  const [formData, setFormData] = useState(user);
  const [editing, setEditing] = useState(false);
  const router = useRouter();

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };


  const handleGoToArticles = () => {
    router.push('/articles');
  };

  useEffect(() => {
    api.get("/me").then(res => setUser(res.data));
  }, []);

  const handleSave = async () => {
    try {
      await api.put("/me", formData);
      setMessage("Profil mis à jour !");
    } catch {
      setMessage("Erreur de mise à jour");
    }
  };

  return (
    <>
      <Navbar />
      <div className={styles.container}>
      <div className={styles.card}>
        <h1 className={styles.title}>Mon compte</h1>

        <div className={styles.field}>
          <label className={styles.label}>Nom :</label>
          {editing ? (
            <input
              className={styles.input}
              type="text"
              name="name"
              value={formData.name}
              onChange={handleChange}
            />
          ) : (
            <p className={styles.text}>{user.name}</p>
          )}
        </div>

        <div className={styles.field}>
          <label className={styles.label}>Email :</label>
          {editing ? (
            <input
              className={styles.input}
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
            />
          ) : (
            <p className={styles.text}>{user.email}</p>
          )}
        </div>

        <div className={styles.buttons}>
          {editing ? (
            <button onClick={handleSave} className={styles.saveBtn}>
              💾 Enregistrer
            </button>
          ) : (
            <button onClick={() => setEditing(true)} className={styles.editBtn}>
              ✏️ Modifier mes infos
            </button>
          )}

          <button onClick={handleGoToArticles} className={styles.articleBtn}>
            📰 Gérer mes articles
          </button>
        </div>
      </div>
      {message && <p>{message}</p>}
    </div>
    </>
  );
}
