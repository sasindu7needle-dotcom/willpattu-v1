from playwright.sync_api import sync_playwright
from pathlib import Path
with sync_playwright() as p:
 browser=p.chromium.launch()
 page=browser.new_page(viewport={'width':1440,'height':1000},device_scale_factor=1)
 errors=[];page.on('pageerror',lambda e:errors.append(str(e)))
 page.goto('http://127.0.0.1:8765',wait_until='networkidle');page.screenshot(path='storage/app/home-desktop.png',full_page=True)
 assert page.locator('h1').inner_text()=='WILPATTU\nPARK'
 assert page.evaluate('document.documentElement.scrollWidth <= innerWidth')
 page.locator('.menu-toggle').click();assert page.locator('#site-menu').is_visible();page.keyboard.press('Escape');assert not page.locator('#site-menu').is_visible()
 page.goto('http://127.0.0.1:8765/gallery',wait_until='networkidle');page.locator('[data-filter="Nature"]').click();assert page.locator('.gallery-item:visible').count()==2
 page.locator('.gallery-item:visible').first.click();assert page.locator('#lightbox').is_visible();page.keyboard.press('Escape');assert not page.locator('#lightbox').is_visible()
 page.goto('http://127.0.0.1:8765/faq');page.locator('summary').first.click();assert page.locator('details').first.get_attribute('open') is not None
 page.set_viewport_size({'width':320,'height':740});page.goto('http://127.0.0.1:8765',wait_until='networkidle');assert page.evaluate('document.documentElement.scrollWidth <= innerWidth');page.set_viewport_size({'width':390,'height':844})
 for route in ['/','/stay','/safaris','/wildlife','/experiences','/dining','/offers','/plan-your-stay','/getting-here','/about','/sustainability','/gallery','/guest-stories','/journal','/faq','/contact']:
  page.goto('http://127.0.0.1:8765'+route,wait_until='networkidle')
  assert page.evaluate('document.documentElement.scrollWidth <= innerWidth'),route+' overflows'
  assert page.locator('img[src]').evaluate_all('(imgs)=>imgs.every(i=>!i.complete || i.naturalWidth>0)'),route+' broken images'
  if route=='/':page.screenshot(path='storage/app/home-mobile.png',full_page=True)
  if route=='/contact':page.screenshot(path='storage/app/contact-mobile.png',full_page=True)
 assert not errors,errors
 browser.close()
 print('Browser checks passed: desktop/mobile layout, images, menu, gallery filters/lightbox, FAQ; all 16 pages.')


