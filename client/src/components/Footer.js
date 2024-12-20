import React from 'react';
import { Link } from 'react-router-dom';
import '../styles/Footer.css';

const Footer = () => {
  return (
    <footer className="footer">
      <div className="container footer-content">
        <div className="footer-section">
          <h3>About Us</h3>
          <p>Professional camera rental service providing high-quality equipment for photographers and videographers.</p>
        </div>
        
        <div className="footer-section">
          <h3>Quick Links</h3>
          <ul>
            <li><Link to="/terms">Terms of Service</Link></li>
            <li><Link to="/privacy">Privacy Policy</Link></li>
            <li><Link to="/contact">Contact Us</Link></li>
          </ul>
        </div>
        
        <div className="footer-section">
          <h3>Contact Info</h3>
          <p>Email: info@camerarent.com</p>
          <p>Phone: (555) 123-4567</p>
          <p>Address: 123 Camera Street, Photo City</p>
        </div>
      </div>
      
      <div className="footer-bottom">
        <p>&copy; 2024 CameraRent. All rights reserved.</p>
      </div>
    </footer>
  );
};

export default Footer;
