import React from 'react';
import Icon from '@gravityforms/components/react/admin/elements/Icon';
import { Link } from 'react-router-dom';
import './NavBar.css';

interface NavItem {
  id: string;
  label: string;
  path: string;
  icon: string | React.ReactNode;
  isActive?: boolean;
  bottom?: boolean;
}

interface NavBarProps {
  items: NavItem[];
}

const NavBar: React.FC<NavBarProps> = ({ items }) => {
  const mainItems = items.filter(item => !item.bottom);
  const bottomItems = items.filter(item => item.bottom);

  const renderNavItems = (items: NavItem[]) => (
    items.map((item) => (
      <li
        key={item.id}
        className={`gform-router-nav-bar__item ${item.isActive ? 'gform-router-nav-bar__item--active' : ''} spellbook-app__nav-bar-item spellbook-app__nav-bar-item--${item.id}`}
      >
        <Link
          aria-labelledby={`gform-router-nav-bar__item-text--${item.id}`}
          className={`gform-router-nav-bar__item-link gform-button gform-button--icon-white gform-button--size-height-m spellbook-app__nav-bar-item-link spellbook-app__nav-bar-item-link--${item.id}`}
          to={item.path}
        >
          {
            typeof item.icon === 'string' ? (
              <Icon icon={item.icon} customClasses="gform-router-nav-bar__item-icon" />
            ) : item.icon
          }
        </Link>
        <span
          className="gform-text gform-text--color-port gform-typography--size-text-xs gform-typography--weight-medium gform-router-nav-bar__item-text spellbook-app__nav-bar-item-text"
          id={`gform-router-nav-bar__item-text--${item.id}`}
        >
          {item.label === 'Free Plugins' ? (
            <>
              <span className="nav-text-full">Free Plugins</span>
              <span className="nav-text-short">Free</span>
            </>
          ) : item.label}
        </span>
      </li>
    ))
  );

  return (
    <nav className="gform-router-nav-bar gform-router-nav-bar--icon-button spellbook-app__nav-bar">
      <ul className="gform-router-nav-bar__list">
        {renderNavItems(mainItems)}
      </ul>
      {bottomItems.length > 0 && (
        <ul className="gform-router-nav-bar__list gform-router-nav-bar__list--bottom">
          {renderNavItems(bottomItems)}
        </ul>
      )}
    </nav>
  );
};

export default NavBar;
