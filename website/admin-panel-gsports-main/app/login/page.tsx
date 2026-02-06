'use client';

import React from "react"

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { signInWithEmailAndPassword } from 'firebase/auth';
import { auth } from '@/lib/firebase';
import { useAuth } from '@/lib/auth-context';
import { AlertCircle } from 'lucide-react';
import { Alert, AlertDescription } from '@/components/ui/alert';
import Particles from '@/components/ui/particles';
import Image from 'next/image';
import GSCLoader from '@/components/ui/gsc-loader';


export default function LoginPage() {
  const router = useRouter();
  const { user, userData, loading: authLoading } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  // Redirect if already logged in
  useEffect(() => {
    if (!authLoading && user && userData) {
      router.push('/dashboard');
    }
  }, [user, userData, authLoading, router]);

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      await signInWithEmailAndPassword(auth, email, password);
    } catch (error: any) {
      const errorMessage =
        error.code === 'auth/user-not-found'
          ? 'Email not found'
          : error.code === 'auth/wrong-password'
            ? 'Incorrect password'
            : error.code === 'auth/invalid-email'
              ? 'Invalid email format'
              : 'Failed to login. Please try again.';
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  };

  // Show loading if auth is still loading
  if (authLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center" style={{ background: 'linear-gradient(135deg, #1a1a1a 0%, #000000 100%)' }}>
        <GSCLoader />
      </div>
    );
  }

  return (
    <div className="min-h-screen flex items-center justify-center relative overflow-hidden">
      <Particles />
      
      <div className="login-box relative z-10">
        <div className="text-center mb-8">
          <div className="flex flex-col items-center justify-center mb-4">
            <Image 
              src="/logogsc.png" 
              alt="GSC Logo" 
              width={180} 
              height={180} 
              className="mb-3"
            />
            <div>
              <p className="text-gray-400 text-sm">Admin Panel</p>
            </div>
          </div>
        </div>
        
        <p className="login-title">Login</p>
        
        <form onSubmit={handleLogin}>
          {error && (
            <Alert variant="destructive" className="mb-4 bg-red-900/20 border-red-500">
              <AlertCircle className="h-4 w-4" />
              <AlertDescription className="text-red-300">{error}</AlertDescription>
            </Alert>
          )}
          
          <div className="user-box">
            <input
              required
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              disabled={loading}
            />
            <label>Email</label>
          </div>
          
          <div className="user-box">
            <input
              required
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              disabled={loading}
            />
            <label>Password</label>
          </div>
          
          <button type="submit" className="submit-btn" disabled={loading}>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            {loading ? 'Logging in...' : 'Submit'}
          </button>
        </form>
        
        
      </div>
      
      <style jsx>{`
        .login-box {
          position: absolute;
          top: 50%;
          left: 50%;
          width: 400px;
          padding: 40px;
          margin: 20px auto;
          transform: translate(-50%, -55%);
          background: rgba(0,0,0,.9);
          box-sizing: border-box;
          box-shadow: 0 15px 25px rgba(0,0,0,.6);
          border-radius: 10px;
          border: 1px solid rgba(255, 0, 0, 0.2);
        }
        
        .login-title {
          margin: 0 0 30px;
          padding: 0;
          color: #fff;
          text-align: center;
          font-size: 1.5rem;
          font-weight: bold;
          letter-spacing: 1px;
        }
        
        .user-box {
          position: relative;
        }
        
        .user-box input {
          width: 100%;
          padding: 10px 0;
          font-size: 16px;
          color: #fff;
          margin-bottom: 30px;
          border: none;
          border-bottom: 1px solid #fff;
          outline: none;
          background: transparent;
        }
        
        .user-box label {
          position: absolute;
          top: 0;
          left: 0;
          padding: 10px 0;
          font-size: 16px;
          color: #fff;
          pointer-events: none;
          transition: .5s;
        }
        
        .user-box input:focus ~ label,
        .user-box input:valid ~ label {
          top: -20px;
          left: 0;
          color: #ff0000;
          font-size: 12px;
        }
        
        .user-box input:focus {
          border-bottom-color: #ff0000;
        }
        
        .submit-btn {
          position: relative;
          display: inline-block;
          padding: 10px 20px;
          font-weight: bold;
          color: #fff;
          font-size: 16px;
          text-decoration: none;
          text-transform: uppercase;
          overflow: hidden;
          transition: .5s;
          margin-top: 40px;
          letter-spacing: 3px;
          background: transparent;
          border: none;
          cursor: pointer;
          width: 100%;
        }
        
        .submit-btn:hover {
          background: #ff0000;
          color: #fff;
          border-radius: 5px;
        }
        
        .submit-btn:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }
        
        .submit-btn span {
          position: absolute;
          display: block;
        }
        
        .submit-btn span:nth-child(1) {
          top: 0;
          left: -100%;
          width: 100%;
          height: 2px;
          background: linear-gradient(90deg, transparent, #ff0000);
          animation: btn-anim1 1.5s linear infinite;
        }
        
        @keyframes btn-anim1 {
          0% { left: -100%; }
          50%, 100% { left: 100%; }
        }
        
        .submit-btn span:nth-child(2) {
          top: -100%;
          right: 0;
          width: 2px;
          height: 100%;
          background: linear-gradient(180deg, transparent, #ff0000);
          animation: btn-anim2 1.5s linear infinite;
          animation-delay: .375s;
        }
        
        @keyframes btn-anim2 {
          0% { top: -100%; }
          50%, 100% { top: 100%; }
        }
        
        .submit-btn span:nth-child(3) {
          bottom: 0;
          right: -100%;
          width: 100%;
          height: 2px;
          background: linear-gradient(270deg, transparent, #ff0000);
          animation: btn-anim3 1.5s linear infinite;
          animation-delay: .75s;
        }
        
        @keyframes btn-anim3 {
          0% { right: -100%; }
          50%, 100% { right: 100%; }
        }
        
        .submit-btn span:nth-child(4) {
          bottom: -100%;
          left: 0;
          width: 2px;
          height: 100%;
          background: linear-gradient(360deg, transparent, #ff0000);
          animation: btn-anim4 1.5s linear infinite;
          animation-delay: 1.125s;
        }
        
        @keyframes btn-anim4 {
          0% { bottom: -100%; }
          50%, 100% { bottom: 100%; }
        }
        
        .signup-text {
          color: #aaa;
          font-size: 14px;
          text-align: center;
          margin-top: 20px;
        }
      `}</style>
    </div>
  );
}
