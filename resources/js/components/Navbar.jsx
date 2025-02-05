import React, { useState, useEffect, useRef } from 'react';
import { ChevronDown, Bell, Settings, HelpCircle, LogOut, Menu, X, Shield, Monitor, Server, FileText } from 'lucide-react';

const Navbar = () => {
  const [activeDropdown, setActiveDropdown] = useState(null);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const dropdownRef = useRef(null);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
        setActiveDropdown(null);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const toggleDropdown = (menu) => {
    setActiveDropdown(activeDropdown === menu ? null : menu);
  };

  const dropdownMenus = {
    dashboards: [
      { icon: Monitor, title: 'Overview Dashboard', description: 'System-wide overview and stats' },
      { icon: Shield, title: 'Security Dashboard', description: 'Threat monitoring and analysis' },
      { icon: Server, title: 'Network Dashboard', description: 'Network traffic and performance' }
    ],
    products: [
      { icon: Shield, title: 'Endpoint Protection', description: 'Secure your devices' },
      { icon: Server, title: 'Firewall', description: 'Network security' },
      { icon: FileText, title: 'Cloud Security', description: 'Cloud workload protection' }
    ],
  };

  return (
    <>
      <nav className="bg-blue-700 text-white h-14 flex items-center px-4 fixed top-0 w-full z-50 shadow-lg">
        <div className="flex items-center gap-6 h-full w-full max-w-7xl mx-auto">
          {/* Logo */}
          <a href="/" className="font-semibold text-lg flex items-center gap-2">
            <Shield size={24} />
            <span className="hidden md:inline">Sophos</span>
          </a>

          {/* Mobile Menu Button */}
          <button
            className="md:hidden ml-auto"
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
          >
            {isMobileMenuOpen ? <X size={24} /> : <Menu size={24} />}
          </button>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center gap-2 h-full flex-1">
            {Object.entries(dropdownMenus).map(([key, items]) => (
              <div key={key} className="relative h-full">
                <button
                  onClick={() => toggleDropdown(key)}
                  className="flex items-center gap-1 px-3 h-full hover:bg-blue-800 transition-colors duration-200">
                  <span>{key.charAt(0).toUpperCase() + key.slice(1)}</span>
                  <ChevronDown size={16} className={`transform transition-transform duration-200 ${activeDropdown === key ? 'rotate-180' : ''}`} />
                </button>
                {activeDropdown === key && (
                  <div className="absolute top-full left-0 w-64 bg-white rounded-lg shadow-lg mt-1">
                    {items.map((item, index) => (
                      <a key={index} href="#" className="flex items-center gap-3 p-3 hover:bg-gray-50 text-gray-700 transition-colors duration-200">
                        <item.icon size={20} className="text-blue-600" />
                        <div>
                          <div className="font-medium">{item.title}</div>
                          <div className="text-sm text-gray-500">{item.description}</div>
                        </div>
                      </a>
                    ))}
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>
      </nav>

      {/* Mobile Menu */}
      <div className={`
        fixed inset-0 bg-blue-700 z-40 transition-transform duration-300 ease-in-out transform
        ${isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'}
        md:hidden pt-14
      `}>
        <div className="p-4 space-y-4">
          {Object.entries(dropdownMenus).map(([key, items]) => (
            <div key={key} className="space-y-2">
              <div className="font-semibold text-white px-4 py-2">
                {key.charAt(0).toUpperCase() + key.slice(1)}
              </div>
              {items.map((item, index) => (
                <a key={index} href="#" className="flex items-center gap-3 px-4 py-2 text-white hover:bg-blue-600 rounded-lg">
                  <item.icon size={20} />
                  <div>
                    <div className="font-medium">{item.title}</div>
                    <div className="text-sm text-blue-200">{item.description}</div>
                  </div>
                </a>
              ))}
            </div>
          ))}
        </div>
      </div>
    </>
  );
};

export default Navbar;
