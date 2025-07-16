import React from 'react';
import './PrimarySideBar.css';
import NavBar from './NavBar';
import { useLocation } from 'react-router-dom';
import Perks from '../svgs/perks.svg';
import Connect from '../svgs/connect.svg';
import Shop from '../svgs/shop.svg';
import { home, tag, key } from '@wordpress/icons';
import { Icon } from '@wordpress/components';
import All from '../svgs/all.svg';
import FreePlugins from '../svgs/free-plugins.svg';

const PrimarySideBar: React.FC = () => {
  const location = useLocation();

  const navItems = [
	{
		id: 'all',
		label: 'All',
		path: '/',
		icon: <All width={30} height={30} />,
		isActive: location.pathname === '/'
	  },
    {
      id: 'perks',
      label: 'Perks',
      path: '/perks',
      icon: <Perks width={30} height={30} />,
      isActive: location.pathname === '/'
    },
    {
      id: 'connect',
      label: 'Connect',
      path: '/connect',
      icon: <Connect width={30} height={30} />,
      isActive: location.pathname === '/'
    },
    {
      id: 'shop',
      label: 'Shop',
      path: '/shop',
      icon: <Shop width={30} height={30} />,
      isActive: location.pathname === '/shop'
    },
	{
		id: 'free-plugins',
		label: 'Free Plugins',
		path: '/free-plugins',
		icon: <FreePlugins width={34} height={34} />,
		isActive: location.pathname === '/free-plugins'
	},
	{
		id: 'license',
		label: 'Licenses',
		path: '/licenses',
		bottom: true,
		icon: <Icon icon={key} size={30} />,
		isActive: location.pathname === '/licenses'
	},
  ];

  return <NavBar items={navItems} />;
};

export default PrimarySideBar;
