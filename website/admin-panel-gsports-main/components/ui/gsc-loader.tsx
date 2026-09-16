'use client';

import React from 'react';

const GSCLoader = () => {
  return (
    <div className="flex justify-center items-center">
      <div className="relative w-32 h-32 flex justify-center items-center">
        {/* Main bars */}
        <div className="absolute w-4 h-16 bg-red-500 animate-pulse" 
             style={{ 
               animation: 'move-h 1.2s infinite cubic-bezier(0.65, 0.05, 0.36, 1)' 
             }} />
        <div className="absolute w-4 h-15 bg-white transform rotate-90" 
             style={{ 
               animation: 'move-v 1.2s infinite cubic-bezier(0.65, 0.05, 0.36, 1)' 
             }} />
        
        {/* Effect elements */}
        <div className="absolute w-px h-10 bg-red-500 opacity-30 top-0 left-2 animate-pulse" />
        <div className="absolute w-15 h-px bg-white opacity-80 top-2 left-0" />
        <div className="absolute top-2 left-3 text-white font-black text-lg animate-spin">G</div>
        <div className="absolute w-px h-10 bg-red-500 opacity-30 bottom-0 right-2" />
        <div className="absolute w-10 h-px bg-white opacity-30 bottom-0 right-0" />
        <div className="absolute bottom-0 right-0 text-red-500 text-2xl animate-bounce">*</div>
        
        {/* Diagonal lines */}
        <div className="absolute w-px h-5 bg-white bottom-0 left-0 transform rotate-45" 
             style={{ 
               animation: 'height 1s infinite cubic-bezier(0.65, 0.05, 0.36, 1)' 
             }} />
        <div className="absolute w-5 h-px bg-red-500 bottom-1/2 left-0" 
             style={{ 
               animation: 'width 1.5s infinite cubic-bezier(0.65, 0.05, 0.36, 1)' 
             }} />
      </div>
      
      <style jsx>{`
        @keyframes move-h {
          0% { top: 0; opacity: 0; }
          25% { opacity: 1; }
          50% { top: 30%; opacity: 1; }
          75% { opacity: 1; }
          100% { top: 100%; opacity: 0; }
        }
        
        @keyframes move-v {
          0% { left: 0; opacity: 0; }
          25% { opacity: 1; }
          50% { left: 45%; opacity: 1; }
          75% { opacity: 1; }
          100% { left: 100%; opacity: 0; }
        }
        
        @keyframes height {
          0% { bottom: 0%; left: 0%; height: 0px; }
          25% { height: 90px; }
          50% { bottom: 100%; left: 100%; height: 90px; }
          75% { height: 0px; }
          100% { bottom: 0%; left: 0%; height: 0px; }
        }
        
        @keyframes width {
          0% { left: 0%; width: 0px; }
          50% { left: 100%; width: 90px; }
          100% { left: 0%; width: 0px; }
        }
      `}</style>
    </div>
  );
};

export default GSCLoader;