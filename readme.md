### Maker Faire WordPress theme
https://makerfaire.com/

Built with:
- Bootstrap (`^3.3.4`)
- LESS
- ...

Relevant: https://docs.google.com/document/d/1thSVdCn4Li_l7pVD6hFJvTrQuv-f29HDQMhX_D0vLO8

##### How to compile LESS, Bootstrap, JS for production:
Setup:
- First download Node if you don't have it installed https://nodejs.org/en/download/
- Next install the grunt command line interface if you don't have it `npm install -g grunt-cli`

Build:
- cd into your local makerfaire theme directory e.g. `/wp-content/themes/makerfaire/`
- run the command `npm install` to install dev dependencies listed in package.json
- run the command `grunt default` to compile and watch files (equivalent to just `grunt`)

##### How to compile LESS, Bootstrap, JS for development:
- perform same steps as above, but optionally use the task `grunt dev` for unminified css and js with source line comments, for easier development and debugging


---
---
---

```
                      :mmmmm:                   
                   :mmmmmmmmmmm:                
                 :m::mmmmmmmmm::m:              
                :m:   mmmmmmm   :m:             
                mmm::mmmmmmmmm::mmm.            
               .....................            
          ....mmmmmmmmm:::::mmmmmmmmm....       
      :mmmmmm:mmmmm |mm     mm| mmmmmm:mmmmmm:  
     :mmmmmmm:mmmm  |mmm   mmm|  mmmm:mmmmmmmm: 
    :mmmmmmmm:::::  |mmmm.mmmm|  :::::mmmmmmmmm:
    :mmmmmmmm:mmmm  |mm mmm mm|  mmmm:mmmmmmmmm:
     :mmmmmm: :mmmm |mm  v  mm| mmmm: :mmmmmm:  
     :mmmmmm: :mmmmmmmm:...:mmmmmmmm: :mmmmmm:  
     :mmmmmm:  :mmmmmmmmmmmmmmmmmmmm: :mmmmmm:  
     :mmmmmm:   :mmmmmm:    :mmmmmm:  :mmmmmm:  
    :mmmmmmm:   :mmmmmm:    :mmmmmm:  :mmmmmmm: 
    .........   :.....:      :.....:  ......... 
     :mmmmmmm: :mmmmmmm:    :mmmmmmm: :mmmmmmm: 
      :mmmmmm: :mmmmmmm:    :mmmmmmm: :mmmmmm:  
        mmmm  :mmmmmmmm:    :mmmmmmmm:  mmmm    
              :mmmmmmmm:    :mmmmmmmm:          
             mmmmmmmmmmm    mmmmmmmmmmm         
```
