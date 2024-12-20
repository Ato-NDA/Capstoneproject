import React, { useState } from 'react';
import '../styles/Home.css';

const Home = () => {
  const [searchQuery, setSearchQuery] = useState('');
  
  const featuredCameras = [
    {
      id: 1,
      title: 'Canon EOS R5',
      price: 150,
      image: '/images/canon-r5.jpg',
      category: 'Mirrorless'
    },
    {
      id: 2,
      title: 'Sony A7III',
      price: 120,
      image: '/images/sony-a7iii.jpg',
      category: 'Mirrorless'
    },
    {
      id: 3,
      title: 'Nikon D850',
      price: 130,
      image: '/images/nikon-d850.jpg',
      category: 'DSLR'
    },
    // Add more featured cameras as needed
  ];

  const handleSearch = (e) => {
    e.preventDefault();
    // Implement search functionality
  };

  return (
    <div className="home">
      <div className="hero-section">
        <h1>Professional Cameras for Rent</h1>
        <p>Capture your perfect moment with our premium equipment</p>
        <form onSubmit={handleSearch} className="search-container">
          <input
            type="text"
            className="search-bar"
            placeholder="Search for cameras..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
          />
          <button type="submit" className="btn">Search</button>
        </form>
      </div>

      <section className="featured-section">
        <div className="container">
          <h2>Featured Cameras</h2>
          <div className="camera-grid">
            {featuredCameras.map(camera => (
              <div key={camera.id} className="camera-card">
                <img src={camera.image} alt={camera.title} />
                <div className="camera-card-content">
                  <h3>{camera.title}</h3>
                  <p className="price">${camera.price}/day</p>
                  <button className="btn">Rent Now</button>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="categories-section">
        <div className="container">
          <h2>Browse by Category</h2>
          <div className="categories-grid">
            <div className="category-card">DSLR Cameras</div>
            <div className="category-card">Mirrorless Cameras</div>
            <div className="category-card">Lenses</div>
            <div className="category-card">Accessories</div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Home;
