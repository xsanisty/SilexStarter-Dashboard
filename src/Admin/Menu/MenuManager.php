<?php

namespace Xsanisty\Admin\Menu;

use Exception;

class MenuManager
{
    /**
     * A list of MenuContainer object.
     *
     * @var array of Xsanisty\Admin\Menu\MenuContainer
     */
    protected $menuContainers = [];

    /**
     * Create new MenuContainer object and assign to menu container lists.
     *
     * @param string $name MenuContainer name
     *
     * @return Xsanisty\Admin\Menu\MenuContainer
     */
    public function create($name)
    {
        $this->menuContainers[$name] = new MenuContainer($name);

        return $this->menuContainers[$name];
    }

    /**
     * Create menu structure from array.
     *
     * @param array $menu
     */
    public function createFromArray(array $menus)
    {
        foreach ($menus as $name => $items) {
            if (!isset($this->menuContainers[$name])) {
                $this->create($name);
            }

            $menu = $this->get($name);

            foreach ($items as $itemName => $itemConfig) {
                $menuItem = $menu->createItem($itemName, $itemConfig);

                if (isset($itemConfig['submenu']) && $itemConfig['submenu']) {
                    $this->addSubMenuArray($menuItem, $itemConfig['submenu']);
                }
            }
        }
    }

    protected function addSubMenuArray(MenuItem $menu, array $submenu)
    {
        foreach ($submenu as $menuName => $menuConfig) {
            $menu->addChildren($menuName, $menuConfig);

            if (isset($menuConfig['submenu']) && $menuConfig['submenu']) {
                $this->addSubMenuArray($menuItem, $menuConfig['submenu']);
            }
        }
    }

    /**
     * Get MenuContainer object based on it's name.
     *
     * @param string $name MenuContainer name
     *
     * @return Xsanisty\Admin\Menu\MenuContainer
     */
    public function get($name)
    {
        if (isset($this->menuContainers[$name])) {
            return $this->menuContainers[$name];
        }

        throw new Exception("Can not find menu with name: $name");
    }

    /**
     * Render specified MenuContainer.
     *
     * @param string $name MenuContainer name
     *
     * @return string
     */
    public function render($name)
    {
        return $this->get($name)->render();
    }
}
