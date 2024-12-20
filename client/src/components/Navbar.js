import React from 'react';
import { Link } from 'react-router-dom';
import '../styles/Navbar.css';

const Navbar = () => {
  return (
    <nav className="navbar">
      <div className="container nav-container">
        <Link to="/" className="logo">
          CameraRent
        </Link>
        
        <div className="nav-links">
          <Link to="/" className="nav-link">Home</Link>
          <Link to="/catalog" className="nav-link">Catalog</Link>
          <Link to="/reservations" className="nav-link">Reservations</Link>
          <Link to="/account" className="nav-link">Account</Link>
        </div>
      </div>
    </nav>
  );
};

export default Navbar;
