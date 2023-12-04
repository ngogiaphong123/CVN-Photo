import ReactDOM from 'react-dom/client'
import App from './App.tsx'
import './index.css'
import ReduxProvider from '@redux/provider'
import { BrowserRouter as Router } from 'react-router-dom'
import { Toaster } from '@/components/ui/toaster.tsx'

ReactDOM.createRoot(document.getElementById('root')!).render(
  <ReduxProvider>
    <Router>
      <App />
      <Toaster />
    </Router>
  </ReduxProvider>,
)
