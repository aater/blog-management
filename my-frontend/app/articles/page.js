'use client'

import { useState } from 'react';
import Navbar from '../components/Navbar';
import styles from '../styles/Articles.module.css';
import { useEffect } from 'react';
import api from "../lib/api";

export default function ArticlesPage() {
  const [articles, setArticles] = useState([]);
  const [newArticle, setNewArticle] = useState({ title: "", content: "" });

  const [editingId, setEditingId] = useState(null);
  const [formData, setFormData] = useState({ title: '', content: '' });

  useEffect(() => {
    api.get("/articles").then(res => setArticles(res.data));
  }, []);

  const handleAdd = async () => {
    if (!formData.title.trim() || !formData.content.trim()) {
      alert('Veuillez remplir tous les champs.');
      return;
    }
    const res = await api.post("/articles", newArticle);
    setArticles([...articles, res.data]);
    setFormData({ title: '', content: '' });
  };

  const handleSaveEdit = async () => {
    const res = await api.put(`/articles/${editingId}`, formData);
    setArticles(
      articles.map((a) => (editingId === formData.id ? res.data : a))
    );
    setEditingId(null);
    setFormData({ title: '', content: '' });
  };

  const handleDelete = async (id) => {
    if (window.confirm('Supprimer cet article ?')) {
        await api.delete(`/articles/${id}`);
        setArticles(articles.filter(a => a.id !== id));
    }
  };

  const handleEdit = (article) => {
    setEditingId(article.id);
    setFormData({ title: article.title, content: article.content });
  };

  return (
    <>
    <Navbar />
    <div className={styles.container}>
      <div className={styles.wrapper}>
        <h1 className={styles.title}>Gestion des articles</h1>

        <div className={styles.form}>
          <input
            className={styles.input}
            type="text"
            name="title"
            placeholder="Titre de l'article"
            value={formData.title}
            onChange={(e) => setNewArticle({ ...newArticle, title: e.target.value })}
          />
          <textarea
            className={styles.textarea}
            name="content"
            placeholder="Contenu de l'article"
            value={formData.content}
            onChange={(e) => setNewArticle({ ...newArticle, content: e.target.value })}
          />
          {editingId ? (
            <button className={styles.saveBtn} onClick={handleSaveEdit}>
              💾 Enregistrer les modifications
            </button>
          ) : (
            <button className={styles.addBtn} onClick={handleAdd}>
              ➕ Ajouter l’article
            </button>
          )}
        </div>

        <div className={styles.list}>
          {articles.length === 0 ? (
            <p className={styles.empty}>Aucune article pour le moment.</p>
          ) : (
            articles.map((article) => (
              <div key={article.id} className={styles.articleCard}>
                <div className={styles.header}>
                  <h2 className={styles.articleTitle}>{article.title}</h2>
                  <div className={styles.meta}>
                    <span>✍️ {article.author}</span>
                    <span>📅 {article.date}</span>
                  </div>
                </div>
                <p className={styles.content}>{article.content}</p>
                <div className={styles.actions}>
                  <button
                    className={styles.editBtn}
                    onClick={() => handleEdit(article)}
                  >
                    ✏️ Modifier
                  </button>
                  <button
                    className={styles.deleteBtn}
                    onClick={() => handleDelete(article.id)}
                  >
                    🗑️ Supprimer
                  </button>
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </div>
    </>
  );
}
